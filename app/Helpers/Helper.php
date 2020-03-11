<?php 

namespace App\Helpers;

class Helper
{
    
    static function curl($url)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$headers = [];
		// This function is called by curl for each header received
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			function($curl, $header) use (&$headers)
			{
				$len = strlen($header);
				$header = explode(':', $header, 2);
				
				// Ignore invalid headers
				if (count($header) < 2)
					return $len;

				$name = strtolower(trim($header[0]));
				
				if (!array_key_exists($name, $headers))
					$headers[$name] = [trim($header[1])];
				else
					$headers[$name][] = trim($header[1]);

				return $len;
			}
		);

		$body = curl_exec($ch);
		curl_close($ch);

		$result = compact('body', 'headers');

        return $result;
    }


    /**
     * Calculate the conversion rate for clicks
     */
    static function conversionRate($clicks, $conversion)
    {
    	// return 0 if clicks is zero
    	if ( $clicks == 0 )
    		return '0%';


        $calculate = ($conversion / $clicks) * 100;
        $result = round($calculate, 2);

        return $result.'%';
    }


    /**
     * Return the full pixel source code for a given platform
     */
    static function pixelSourceCode($platform, $pixelId)
    {
    	// Facebook Pixel script
    	if ($platform === 'facebook') {
			$script = "
				<script>
					!function(f,b,e,v,n,t,s)
					{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
					n.callMethod.apply(n,arguments):n.queue.push(arguments)};
					if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
					n.queue=[];t=b.createElement(e);t.async=!0;
					t.src=v;s=b.getElementsByTagName(e)[0];
					s.parentNode.insertBefore(t,s)}(window, document,'script',
					'https://connect.facebook.net/en_US/fbevents.js');
					fbq('init', '%d');
					fbq('track', 'PageView');
				</script>

				<noscript><img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id=%d&ev=PageView&noscript=1'/></noscript>
			";

			return sprintf($script, $pixelId, $pixelId);
		}


		// Twitter Pixel script
    	if ($platform === 'twitter') {
			$script = "
				<script>
				!function(e,n,u,a){e.twq||(a=e.twq=function(){a.exe?a.exe.apply(a,arguments):
				a.queue.push(arguments);},a.version='1',a.queue=[],t=n.createElement(u),
				t.async=!0,t.src='//static.ads-twitter.com/uwt.js',s=n.getElementByTagName(u)[0],s.parentNode.insertBefore(t,s))}(window,document,'script');
				// Insert Twitter Pixel ID and Standard Event data below
				twq('init','%d');
				twq('track','PageView');
				</script>
			";

			return sprintf($script, $pixelId);
		}


		// Linkedin Pixel script
    	if ($platform === 'linkedin') {
			$script = "
				<script type='text/javascript'>
                    _linkedin_data_partner_id = %d;
                </script>
                <script type='text/javascript'>
                    (function () {
                        var s = document.getElementsByTagName('script')[0];
                        var b = document.createElement('script');
                        b.type = 'text/javascript';
                        b.async = true;
                        b.src = 'https://snap.licdn.com/li.lms-analytics/insight.min.js';
                        s.parentNode.insertBefore(b, s);
                    })();
                </script>
                <noscript><img height='1' width='1' style='display:none;' alt='' src='https://dc.ads.linkedin.com/collect/?pid=%d&fmt=gif'/></noscript>
			";

			return sprintf($script, $pixelId, $pixelId);
		}


		// Pinterest Pixel script
    	if ($platform === 'pinterest') {
			$script = "
           	 	<script type='text/javascript'>
                    !function (e) {
                        if (!window.pintrk) {
                            window.pintrk = function () {
                                window.pintrk.queue.push(Array.prototype.slice.call(arguments))
                            };
                            var n = window.pintrk;
                            n.queue = [], n.version = '3.0';
                            var t = document.createElement('script');
                            t.async = !0, t.src = e;
                            var r = document.getElementsByTagName('script')[0];
                            r.parentNode.insertBefore(t, r)
                        }
                    }('https://s.pinimg.com/ct/core.js');
                    pintrk('load', %d);
                    pintrk('page');
                </script>
                <noscript>
                    <img height='1' width='1' style='display:none;' alt=''
                         src='https://ct.pinterest.com/v3/?tid=%d&noscript=1'/>
                </noscript>
			";

			return sprintf($script, $pixelId, $pixelId);
		}


		// Quora Pixel script
    	if ($platform === 'quora') {
			$script = "
               <script>
                    !function (q, e, v, n, t, s) {
                        if (q.qp) return;
                        n = q.qp = function () {
                            n.qp ? n.qp.apply(n, arguments) : n.queue.push(arguments);
                        };
                        n.queue = [];
                        t = document.createElement(e);
                        t.async = !0;
                        t.src = v;
                        s = document.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t, s);
                    }(window, 'script', 'https://a.quora.com/qevents.js');
                    qp('init', %d);
                    qp('track', 'ViewContent');
                </script>

                <noscript><img height='1' width='1' style='display:none' src='https://q.quora.com/_/ad/%d/pixel?tag=ViewContent&noscript=1'/></noscript>
			";

			return sprintf($script, $pixelId, $pixelId);
		}



		// Bing Pixel script
    	if ($platform === 'bing') {
			$script = '
				<script>
				(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[] ,f=function(){var o={ti:"%d"}; o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")} ,n=d.createElement(t),n.src=r,n.async=1,n.onload=n .onreadystatechange=function() {var s=this.readyState;s &&s!=="loaded"&& s!=="complete"||(f(),n.onload=n. onreadystatechange=null)},i= d.getElementsByTagName(t)[0],i. parentNode.insertBefore(n,i)})(window,document,"script"," //bat.bing.com/bat.js","uetq");
				</script>
				<noscript><img src="//bat.bing.com/action/0?ti=%d&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>
			';

			return sprintf($script, $pixelId, $pixelId);
		}



		// Google adwords Pixel script
    	if ($platform === 'google-adwords') {
			$script = "
				<script type='text/javascript'>
				 
				var google_conversion_id = '%s';
				var google_custom_params = window.google_tag_params;
				var google_remarketing_only = true;
				 
				</script>
				<script type='text/javascript' src='//www.googleadservices.com/pagead/conversion.js'>
				</script>
				<noscript>
				<div style='display:inline;'>
				<img height='1' width='1' style='border-style:none;' alt='' src='//googleads.g.doubleclick.net/pagead/viewthroughconversion/%s/?guid=ON&amp;script=0'/>
				</div>
				</noscript>
			";

			return sprintf($script, $pixelId, $pixelId);
		}


		// Google tag manager Pixel script
    	if ($platform === 'google-tag-manager') {
			$script = "
				<script async src='https://www.googletagmanager.com/gtag/js?id=%s'></script>
				<script>

				  window.dataLayer = window.dataLayer || [];

				  function gtag(){dataLayer.push(arguments);}

				  gtag('js', new Date());

				  gtag('config', '%s');

				</script>
			";

			return sprintf($script, $pixelId, $pixelId);
		}





    }

}