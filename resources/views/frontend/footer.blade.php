
</div>

<footer class="footer footer-1 pos-r">
    
    <div class="subscribe-box">
        <div class="container">
            <div class="row subscribe-inner align-items-center">
                <div class="col-md-12 col-sm-12 text-center">

                    <div class="m-auto" style="width: 650px;">
                        <h4 style="background: #ffb58c;text-transform: uppercase;padding: 7px 15px;display: inline-block;border-top-left-radius: 7px;border-bottom-right-radius: 7px;">Share links and drive conversions</h4>
                        <p class="lead">Create custom links to retarget and engage audience on all ad platforms and social networks with absolutely no coding skills necessary</p>
                    	<a href="{{ url('register') }}" class="btn btn-primary">Start Your Free Trial</a>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="secondary-footer">
        <div class="container">
            <div class="copyright text-center text-muted">
            	&copy; {{ date('Y') }} {{ config('app.name') }} - All Rights Reserved
            </div>
        </div>
    </div>

</footer>



<script src="{{ url('assets/frontend/js/jquery.3.3.1.min.js') }}"></script>
<script src="{{ url('assets/frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ url('assets/frontend/js/snap.svg.js') }}"></script>
<script src="{{ url('assets/frontend/js/step.js') }}"></script>
<script src="{{ url('assets/frontend/js/app.js') }}"></script>

</body>
</html>