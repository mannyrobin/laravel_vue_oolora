<template>
<div class="bg-gray p-3 h-100">

    <div class="card shadow-sm">

        <div class="card-header pb-2">
            <h4 class="card-title">General Settings</h4>
        </div>

        <form @submit.prevent="updateSettings()" data-vv-scope="form-general">

            <div class="card-body">

                <p>Configure the applications general settings such as the support email that is included in all system emails that are sent, the platform timezone and date format, the website name and URL. </p>

                <hr class="my-4">

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Website Name</label>
                    <div class="col-lg-9 mb-md-3">
                        <input type="text" name="app.name"
                            :class="['form-control', { 'is-invalid': errors.has('form-general.app.name') }]"
                            v-validate="'required'"
                            v-model="settingsData['app.name']">
                        <div class="invalid-feedback" v-show="errors.has('form-general.app.name')">{{ errors.first('form-general.app.name') }}</div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Website URL</label>
                    <div class="col-lg-9 mb-md-3">
                        <input type="url" name="app.url"
                            :class="['form-control', { 'is-invalid': errors.has('form-general.app.url') }]"
                            v-validate="'required'"
                            v-model="settingsData['app.url']">
                        <div class="invalid-feedback" v-show="errors.has('form-general.app.url')">{{ errors.first('form-general.app.url') }}</div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Timezone</label>
                    <div class="col-lg-9 mb-md-3">
                        <form-select name="app.timezone" v-model="settingsData['app.timezone']">
                            <select-option
                                v-for="item in timezoneData"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                                
                                {{ item.label }}

                            </select-option>
                        </form-select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Date Format</label>
                    <div class="col-lg-9 mb-md-3">
                        <form-select 
                            name="settings.date_format" 
                            v-model="settingsData['settings.date_format']"
                            filterable
                            allow-create
                            default-first-option>
                            
                            <select-option
                                v-for="item in dateFormatData"
                                :key="item.value"
                                :label="item.value"
                                :value="item.value">
                                
                                <span class="float-left">{{ item.value }}</span>
                                <span class="float-right text-muted font-size-sm">{{ item.label }}</span>

                            </select-option>
                        </form-select>    
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Currency Details</label>
                    <div class="col-lg-9 mb-md-3">
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label>Currency Symbol</label>
                                <input type="text" class="form-control" name="settings.currency_symbol" v-model="settingsData['settings.currency_symbol']">
                            </div>

                            <div class="col-md-5">
                                <label>Currency Code</label>
                                <form-select 
                                    name="settings.currency_code" v-model="settingsData['settings.currency_code']">
                                    
                                    <select-option
                                        v-for="item in currencyCode"
                                        :key="item.value"
                                        :label="item.value"
                                        :value="item.value">
                                        
                                        {{ item.label }}
                                        
                                    </select-option>
                                </form-select>
                            </div>

                            <div class="col-md-4">
                                <label>Invoice Prefix</label>
                                <input type="text" class="form-control" name="settings.invoice_prefix" v-model="settingsData['settings.invoice_prefix']">
                            </div>
                        </div>

                    </div>

                </div>


                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Dynamic Domain</label>
                    <div class="col-lg-9 mb-md-3">
                        <input type="url" name="settings.link_shorten_domain"
                            :class="['form-control', { 'is-invalid': errors.has('form-general.settings.link_shorten_domain') }]"
                            v-model="settingsData['settings.link_shorten_domain']">
                        <div class="invalid-feedback" v-show="errors.has('form-general.settings.link_shorten_domain')">{{ errors.first('form-general.settings.link_shorten_domain') }}</div>
                        
                        <div class="form-text font-size-sm text-gray-600">Optional to set a custom domain that will be used for the shorten URL e.g shrt.co</div>

                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-form-label font-weight-semibold col-lg-3">Server IP Address</label>
                    <div class="col-lg-9 mb-md-3">
                        <input type="url" name="settings.server_ip_address"
                            :class="['form-control', { 'is-invalid': errors.has('form-general.settings.server_ip_address') }]"
                            v-model="settingsData['settings.server_ip_address']">
                        <div class="invalid-feedback" v-show="errors.has('form-general.settings.server_ip_address')">{{ errors.first('form-general.settings.server_ip_address') }}</div>
                        
                        <div class="form-text font-size-sm text-gray-600">Needed in order for users to be able to add their custom domains</div>

                    </div>
                </div>

                <div class="form-group row mb-0">
                    <label class="col-form-label font-weight-semibold col-lg-3">Registration Onboarding</label>
                    <div class="col-lg-9 mb-md-3">
                        
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="onboarding" name="onboarding" 
                            :true-value="true" 
                            v-model="planAutoAssign.enable"
                            @change="disableOnboarding">
                            <label class="custom-control-label" for="onboarding">Disable/Enable Onboarding</label>
                        </div>
                        <div class="form-text font-size-sm text-gray-600">Auto assign a user to a free trial plan upon registration without requiring a payment method upfront</div>
                        
                        <transition name="fade">
                            <div v-if="planAutoAssign.enable" class="mt-2 bg-light border py-2 px-3">
                                <div v-for="plan in plans" class="custom-control custom-radio mb-2">
                                    <input type="radio" class="custom-control-input"
                                        :id="plan.id" name="onboarding"
                                        :disabled="!plan.trial_period_days"
                                        :checked="planAutoAssign.current_plan === plan.id"
                                        :value="plan.id"
                                        @change="setOnboarding(plan.id), planAutoAssign.current_plan = plan.id">
                                    <label class="custom-control-label" :for="plan.id">{{ plan.name }}</label>
                                </div>

                                <div class="mt-3 text-gray-600"><span class="font-weight-semibold"><i class="far fa-question-circle"></i> NB:</span> You can only assign a plan that has a free trial period</div>
                            </div>
                        </transition>

                    </div>
                </div>

            </div>

            <div class="card-footer border-top text-center">
                <button-spinner :loading="btnLoadingSettings" class="btn btn-primary" @click.prevent="updateSettings()">Save Settings</button-spinner>
            </div>

        </form>

    </div>

</div>
</template>


<script>
export default {
	data() {
        return {
        	btnLoadingSettings: false,

			settingsData: {},

            planAutoAssign: {
                enable: false,
                current_plan: null
            },

            plans: [],
        	
        	dateFormatData: [
        		{value: "M d, Y", label: "Dec 19, 2010"},
		    	{value: "F j, Y", label: "December 19, 2010"},
		    	{value: "Y-m-d", label: "2010-12-19"},
		    	{value: "m/d/Y", label: "12/19/2010"},
		    	{value: "d/m/Y", label: "19/12/2010"}
		    ],

            currencyCode: [
                {value: "AED", label: "United Arab Emirates dirham (AED)"},
                {value: "AFN", label: "Afghan afghani (AFN)"},
                {value: "ALL", label: "Albanian lek (ALL)"},
                {value: "AMD", label: "Armenian dram (AMD)"},
                {value: "ANG", label: "Netherlands Antillean guilder (ANG)"},
                {value: "AOA", label: "Angolan kwanza (AOA)"},
                {value: "ARS", label: "Argentine peso (ARS)"},
                {value: "AUD", label: "Australian dollar (AUD)"},
                {value: "AWG", label: "Aruban florin (AWG)"},
                {value: "AZN", label: "Azerbaijani manat (AZN)"},
                {value: "BAM", label: "Bosnia and Herzegovina convertible mark (BAM)"},
                {value: "BBD", label: "Barbadian dollar (BBD)"},
                {value: "BDT", label: "Bangladeshi taka (BDT)"},
                {value: "BGN", label: "Bulgarian lev (BGN)"},
                {value: "BHD", label: "Bahraini dinar (BHD)"},
                {value: "BIF", label: "Burundian franc (BIF)"},
                {value: "BMD", label: "Bermudian dollar (BMD)"},
                {value: "BND", label: "Brunei dollar (BND)"},
                {value: "BOB", label: "Bolivian boliviano (BOB)"},
                {value: "BRL", label: "Brazilian real (BRL)"},
                {value: "BSD", label: "Bahamian dollar (BSD)"},
                {value: "BTC", label: "Bitcoin (BTC)"},
                {value: "BTN", label: "Bhutanese ngultrum (BTN)"},
                {value: "BWP", label: "Botswana pula (BWP)"},
                {value: "BYR", label: "Belarusian ruble (BYR)"},
                {value: "BZD", label: "Belize dollar (BZD)"},
                {value: "CAD", label: "Canadian dollar (CAD)"},
                {value: "CDF", label: "Congolese franc (CDF)"},
                {value: "CHF", label: "Swiss franc (CHF)"},
                {value: "CLP", label: "Chilean peso (CLP)"},
                {value: "CNY", label: "Chinese yuan (CNY)"},
                {value: "COP", label: "Colombian peso (COP)"},
                {value: "CRC", label: "Costa Rican colón (CRC)"},
                {value: "CUC", label: "Cuban convertible peso (CUC)"},
                {value: "CUP", label: "Cuban peso (CUP)"},
                {value: "CVE", label: "Cape Verdean escudo (CVE)"},
                {value: "CZK", label: "Czech koruna (CZK)"},
                {value: "DJF", label: "Djiboutian franc (DJF)"},
                {value: "DKK", label: "Danish krone (DKK)"},
                {value: "DOP", label: "Dominican peso (DOP)"},
                {value: "DZD", label: "Algerian dinar (DZD)"},
                {value: "EGP", label: "Egyptian pound (EGP)"},
                {value: "ERN", label: "Eritrean nakfa (ERN)"},
                {value: "ETB", label: "Ethiopian birr (ETB)"},
                {value: "EUR", label: "Euro (EUR)"},
                {value: "FJD", label: "Fijian dollar (FJD)"},
                {value: "FKP", label: "Falkland Islands pound (FKP)"},
                {value: "GBP", label: "Pound sterling (GBP)"},
                {value: "GEL", label: "Georgian lari (GEL)"},
                {value: "GGP", label: "Guernsey pound (GGP)"},
                {value: "GHS", label: "Ghana cedi (GHS)"},
                {value: "GIP", label: "Gibraltar pound (GIP)"},
                {value: "GMD", label: "Gambian dalasi (GMD)"},
                {value: "GNF", label: "Guinean franc (GNF)"},
                {value: "GTQ", label: "Guatemalan quetzal (GTQ)"},
                {value: "GYD", label: "Guyanese dollar (GYD)"},
                {value: "HKD", label: "Hong Kong dollar (HKD)"},
                {value: "HNL", label: "Honduran lempira (HNL)"},
                {value: "HRK", label: "Croatian kuna (HRK)"},
                {value: "HTG", label: "Haitian gourde (HTG)"},
                {value: "HUF", label: "Hungarian forint (HUF)"},
                {value: "IDR", label: "Indonesian rupiah (IDR)"},
                {value: "ILS", label: "Israeli new shekel (ILS)"},
                {value: "IMP", label: "Manx pound (IMP)"},
                {value: "INR", label: "Indian rupee (INR)"},
                {value: "IQD", label: "Iraqi dinar (IQD)"},
                {value: "IRR", label: "Iranian rial (IRR)"},
                {value: "IRT", label: "Iranian toman (IRT)"},
                {value: "ISK", label: "Icelandic króna (ISK)"},
                {value: "JEP", label: "Jersey pound (JEP)"},
                {value: "JMD", label: "Jamaican dollar (JMD)"},
                {value: "JOD", label: "Jordanian dinar (JOD)"},
                {value: "JPY", label: "Japanese yen (JPY)"},
                {value: "KES", label: "Kenyan shilling (KES)"},
                {value: "KGS", label: "Kyrgyzstani som (KGS)"},
                {value: "KHR", label: "Cambodian riel (KHR)"},
                {value: "KMF", label: "Comorian franc (KMF)"},
                {value: "KPW", label: "North Korean won (KPW)"},
                {value: "KRW", label: "South Korean won (KRW)"},
                {value: "KWD", label: "Kuwaiti dinar (KWD)"},
                {value: "KYD", label: "Cayman Islands dollar (KYD)"},
                {value: "KZT", label: "Kazakhstani tenge (KZT)"},
                {value: "LAK", label: "Lao kip (LAK)"},
                {value: "LBP", label: "Lebanese pound (LBP)"},
                {value: "LKR", label: "Sri Lankan rupee (LKR)"},
                {value: "LRD", label: "Liberian dollar (LRD)"},
                {value: "LSL", label: "Lesotho loti (LSL)"},
                {value: "LYD", label: "Libyan dinar (LYD)"},
                {value: "MAD", label: "Moroccan dirham (MAD)"},
                {value: "MDL", label: "Moldovan leu (MDL)"},
                {value: "MGA", label: "Malagasy ariary (MGA)"},
                {value: "MKD", label: "Macedonian denar (MKD)"},
                {value: "MMK", label: "Burmese kyat (MMK)"},
                {value: "MNT", label: "Mongolian tögrög (MNT)"},
                {value: "MOP", label: "Macanese pataca (MOP)"},
                {value: "MRO", label: "Mauritanian ouguiya (MRO)"},
                {value: "MUR", label: "Mauritian rupee (MUR)"},
                {value: "MVR", label: "Maldivian rufiyaa (MVR)"},
                {value: "MWK", label: "Malawian kwacha (MWK)"},
                {value: "MXN", label: "Mexican peso (MXN)"},
                {value: "MYR", label: "Malaysian ringgit (MYR)"},
                {value: "MZN", label: "Mozambican metical (MZN)"},
                {value: "NAD", label: "Namibian dollar (NAD)"},
                {value: "NGN", label: "Nigerian naira (NGN)"},
                {value: "NIO", label: "Nicaraguan córdoba (NIO)"},
                {value: "NOK", label: "Norwegian krone (NOK)"},
                {value: "NPR", label: "Nepalese rupee (NPR)"},
                {value: "NZD", label: "New Zealand dollar (NZD)"},
                {value: "OMR", label: "Omani rial (OMR)"},
                {value: "PAB", label: "Panamanian balboa (PAB)"},
                {value: "PEN", label: "Peruvian nuevo sol (PEN)"},
                {value: "PGK", label: "Papua New Guinean kina (PGK)"},
                {value: "PHP", label: "Philippine peso (PHP)"},
                {value: "PKR", label: "Pakistani rupee (PKR)"},
                {value: "PLN", label: "Polish złoty (PLN)"},
                {value: "PRB", label: "Transnistrian ruble (PRB)"},
                {value: "PYG", label: "Paraguayan guaraní (PYG)"},
                {value: "QAR", label: "Qatari riyal (QAR)"},
                {value: "RON", label: "Romanian leu (RON)"},
                {value: "RSD", label: "Serbian dinar (RSD)"},
                {value: "RUB", label: "Russian ruble (RUB)"},
                {value: "RWF", label: "Rwandan franc (RWF)"},
                {value: "SAR", label: "Saudi riyal (SAR)"},
                {value: "SBD", label: "Solomon Islands dollar (SBD)"},
                {value: "SCR", label: "Seychellois rupee (SCR)"},
                {value: "SDG", label: "Sudanese pound (SDG)"},
                {value: "SEK", label: "Swedish krona (SEK)"},
                {value: "SGD", label: "Singapore dollar (SGD)"},
                {value: "SHP", label: "Saint Helena pound (SHP)"},
                {value: "SLL", label: "Sierra Leonean leone (SLL)"},
                {value: "SOS", label: "Somali shilling (SOS)"},
                {value: "SRD", label: "Surinamese dollar (SRD)"},
                {value: "SSP", label: "South Sudanese pound (SSP)"},
                {value: "STD", label: "São Tomé and Príncipe dobra (STD)"},
                {value: "SYP", label: "Syrian pound (SYP)"},
                {value: "SZL", label: "Swazi lilangeni (SZL)"},
                {value: "THB", label: "Thai baht (THB)"},
                {value: "TJS", label: "Tajikistani somoni (TJS)"},
                {value: "TMT", label: "Turkmenistan manat (TMT)"},
                {value: "TND", label: "Tunisian dinar (TND)"},
                {value: "TOP", label: "Tongan paʻanga (TOP)"},
                {value: "TRY", label: "Turkish lira (TRY)"},
                {value: "TTD", label: "Trinidad and Tobago dollar (TTD)"},
                {value: "TWD", label: "New Taiwan dollar (TWD)"},
                {value: "TZS", label: "Tanzanian shilling (TZS)"},
                {value: "UAH", label: "Ukrainian hryvnia (UAH)"},
                {value: "UGX", label: "Ugandan shilling (UGX)"},
                {value: "USD", label: "United States dollar (USD)"},
                {value: "UYU", label: "Uruguayan peso (UYU)"},
                {value: "UZS", label: "Uzbekistani som (UZS)"},
                {value: "VEF", label: "Venezuelan bolívar (VEF)"},
                {value: "VND", label: "Vietnamese đồng (VND)"},
                {value: "VUV", label: "Vanuatu vatu (VUV)"},
                {value: "WST", label: "Samoan tālā (WST)"},
                {value: "XAF", label: "Central African CFA franc (XAF)"},
                {value: "XCD", label: "East Caribbean dollar (XCD)"},
                {value: "XOF", label: "West African CFA franc (XOF)"},
                {value: "XPF", label: "CFP franc (XPF)"},
                {value: "YER", label: "Yemeni rial (YER)"},
                {value: "ZAR", label: "South African rand (ZAR)"},
                {value: "ZMW", label: "Zambian kwacha (ZMW)"}
            ],

        	timezoneData: [
                {value: "Pacific/Midway", label: "(GMT -11:00) Pacific/Midway"},
                {value: "Pacific/Niue", label: "(GMT -11:00) Pacific/Niue"},
                {value: "Pacific/Pago_Pago", label: "(GMT -11:00) Pacific/Pago_Pago"},
                {value: "Pacific/Samoa", label: "(GMT -11:00) Pacific/Samoa"},
                {value: "US/Samoa", label: "(GMT -11:00) US/Samoa"},
                {value: "HST", label: "(GMT -10:00) HST"},
                {value: "Pacific/Honolulu", label: "(GMT -10:00) Pacific/Honolulu"},
                {value: "Pacific/Johnston", label: "(GMT -10:00) Pacific/Johnston"},
                {value: "Pacific/Rarotonga", label: "(GMT -10:00) Pacific/Rarotonga"},
                {value: "Pacific/Tahiti", label: "(GMT -10:00) Pacific/Tahiti"},
                {value: "US/Hawaii", label: "(GMT -10:00) US/Hawaii"},
                {value: "Pacific/Marquesas", label: "(GMT -09:30) Pacific/Marquesas"},
                {value: "America/Adak", label: "(GMT -09:00) America/Adak"},
                {value: "America/Atka", label: "(GMT -09:00) America/Atka"},
                {value: "Pacific/Gambier", label: "(GMT -09:00) Pacific/Gambier"},
                {value: "US/Aleutian", label: "(GMT -09:00) US/Aleutian"},
                {value: "America/Anchorage", label: "(GMT -08:00) America/Anchorage"},
                {value: "America/Juneau", label: "(GMT -08:00) America/Juneau"},
                {value: "America/Metlakatla", label: "(GMT -08:00) America/Metlakatla"},
                {value: "America/Nome", label: "(GMT -08:00) America/Nome"},
                {value: "America/Sitka", label: "(GMT -08:00) America/Sitka"},
                {value: "America/Yakutat", label: "(GMT -08:00) America/Yakutat"},
                {value: "Pacific/Pitcairn", label: "(GMT -08:00) Pacific/Pitcairn"},
                {value: "US/Alaska", label: "(GMT -08:00) US/Alaska"},
                {value: "America/Creston", label: "(GMT -07:00) America/Creston"},
                {value: "America/Dawson", label: "(GMT -07:00) America/Dawson"},
                {value: "America/Dawson_Creek", label: "(GMT -07:00) America/Dawson_Creek"},
                {value: "America/Ensenada", label: "(GMT -07:00) America/Ensenada"},
                {value: "America/Fort_Nelson", label: "(GMT -07:00) America/Fort_Nelson"},
                {value: "America/Hermosillo", label: "(GMT -07:00) America/Hermosillo"},
                {value: "America/Los_Angeles", label: "(GMT -07:00) America/Los_Angeles"},
                {value: "America/Phoenix", label: "(GMT -07:00) America/Phoenix"},
                {value: "America/Santa_Isabel", label: "(GMT -07:00) America/Santa_Isabel"},
                {value: "America/Tijuana", label: "(GMT -07:00) America/Tijuana"},
                {value: "America/Vancouver", label: "(GMT -07:00) America/Vancouver"},
                {value: "America/Whitehorse", label: "(GMT -07:00) America/Whitehorse"},
                {value: "Canada/Pacific", label: "(GMT -07:00) Canada/Pacific"},
                {value: "Canada/Yukon", label: "(GMT -07:00) Canada/Yukon"},
                {value: "Mexico/BajaNorte", label: "(GMT -07:00) Mexico/BajaNorte"},
                {value: "MST", label: "(GMT -07:00) MST"},
                {value: "PST8PDT", label: "(GMT -07:00) PST8PDT"},
                {value: "US/Arizona", label: "(GMT -07:00) US/Arizona"},
                {value: "US/Pacific", label: "(GMT -07:00) US/Pacific"},
                {value: "US/Pacific-New", label: "(GMT -07:00) US/Pacific-New"},
                {value: "America/Belize", label: "(GMT -06:00) America/Belize"},
                {value: "America/Boise", label: "(GMT -06:00) America/Boise"},
                {value: "America/Cambridge_Bay", label: "(GMT -06:00) America/Cambridge_Bay"},
                {value: "America/Chihuahua", label: "(GMT -06:00) America/Chihuahua"},
                {value: "America/Costa_Rica", label: "(GMT -06:00) America/Costa_Rica"},
                {value: "America/Denver", label: "(GMT -06:00) America/Denver"},
                {value: "America/Edmonton", label: "(GMT -06:00) America/Edmonton"},
                {value: "America/El_Salvador", label: "(GMT -06:00) America/El_Salvador"},
                {value: "America/Guatemala", label: "(GMT -06:00) America/Guatemala"},
                {value: "America/Inuvik", label: "(GMT -06:00) America/Inuvik"},
                {value: "America/Managua", label: "(GMT -06:00) America/Managua"},
                {value: "America/Mazatlan", label: "(GMT -06:00) America/Mazatlan"},
                {value: "America/Ojinaga", label: "(GMT -06:00) America/Ojinaga"},
                {value: "America/Regina", label: "(GMT -06:00) America/Regina"},
                {value: "America/Shiprock", label: "(GMT -06:00) America/Shiprock"},
                {value: "America/Swift_Current", label: "(GMT -06:00) America/Swift_Current"},
                {value: "America/Tegucigalpa", label: "(GMT -06:00) America/Tegucigalpa"},
                {value: "America/Yellowknife", label: "(GMT -06:00) America/Yellowknife"},
                {value: "Canada/Mountain", label: "(GMT -06:00) Canada/Mountain"},
                {value: "Canada/Saskatchewan", label: "(GMT -06:00) Canada/Saskatchewan"},
                {value: "Mexico/BajaSur", label: "(GMT -06:00) Mexico/BajaSur"},
                {value: "MST7MDT", label: "(GMT -06:00) MST7MDT"},
                {value: "Navajo", label: "(GMT -06:00) Navajo"},
                {value: "Pacific/Galapagos", label: "(GMT -06:00) Pacific/Galapagos"},
                {value: "US/Mountain", label: "(GMT -06:00) US/Mountain"},
                {value: "America/Atikokan", label: "(GMT -05:00) America/Atikokan"},
                {value: "America/Bahia_Banderas", label: "(GMT -05:00) America/Bahia_Banderas"},
                {value: "America/Bogota", label: "(GMT -05:00) America/Bogota"},
                {value: "America/Cancun", label: "(GMT -05:00) America/Cancun"},
                {value: "America/Cayman", label: "(GMT -05:00) America/Cayman"},
                {value: "America/Chicago", label: "(GMT -05:00) America/Chicago"},
                {value: "America/Coral_Harbour", label: "(GMT -05:00) America/Coral_Harbour"},
                {value: "America/Eirunepe", label: "(GMT -05:00) America/Eirunepe"},
                {value: "America/Guayaquil", label: "(GMT -05:00) America/Guayaquil"},
                {value: "America/Indiana/Knox", label: "(GMT -05:00) America/Indiana/Knox"},
                {value: "America/Indiana/Tell_City", label: "(GMT -05:00) America/Indiana/Tell_City"},
                {value: "America/Jamaica", label: "(GMT -05:00) America/Jamaica"},
                {value: "America/Knox_IN", label: "(GMT -05:00) America/Knox_IN"},
                {value: "America/Lima", label: "(GMT -05:00) America/Lima"},
                {value: "America/Matamoros", label: "(GMT -05:00) America/Matamoros"},
                {value: "America/Menominee", label: "(GMT -05:00) America/Menominee"},
                {value: "America/Merida", label: "(GMT -05:00) America/Merida"},
                {value: "America/Mexico_City", label: "(GMT -05:00) America/Mexico_City"},
                {value: "America/Monterrey", label: "(GMT -05:00) America/Monterrey"},
                {value: "America/North_Dakota/Beulah", label: "(GMT -05:00) America/North_Dakota/Beulah"},
                {value: "America/North_Dakota/Center", label: "(GMT -05:00) America/North_Dakota/Center"},
                {value: "America/North_Dakota/New_Salem", label: "(GMT -05:00) America/North_Dakota/New_Salem"},
                {value: "America/Panama", label: "(GMT -05:00) America/Panama"},
                {value: "America/Porto_Acre", label: "(GMT -05:00) America/Porto_Acre"},
                {value: "America/Rainy_River", label: "(GMT -05:00) America/Rainy_River"},
                {value: "America/Rankin_Inlet", label: "(GMT -05:00) America/Rankin_Inlet"},
                {value: "America/Resolute", label: "(GMT -05:00) America/Resolute"},
                {value: "America/Rio_Branco", label: "(GMT -05:00) America/Rio_Branco"},
                {value: "America/Winnipeg", label: "(GMT -05:00) America/Winnipeg"},
                {value: "Brazil/Acre", label: "(GMT -05:00) Brazil/Acre"},
                {value: "Canada/Central", label: "(GMT -05:00) Canada/Central"},
                {value: "Chile/EasterIsland", label: "(GMT -05:00) Chile/EasterIsland"},
                {value: "CST6CDT", label: "(GMT -05:00) CST6CDT"},
                {value: "EST", label: "(GMT -05:00) EST"},
                {value: "Jamaica", label: "(GMT -05:00) Jamaica"},
                {value: "Mexico/General", label: "(GMT -05:00) Mexico/General"},
                {value: "Pacific/Easter", label: "(GMT -05:00) Pacific/Easter"},
                {value: "US/Central", label: "(GMT -05:00) US/Central"},
                {value: "US/Indiana-Starke", label: "(GMT -05:00) US/Indiana-Starke"},
                {value: "America/Anguilla", label: "(GMT -04:00) America/Anguilla"},
                {value: "America/Antigua", label: "(GMT -04:00) America/Antigua"},
                {value: "America/Aruba", label: "(GMT -04:00) America/Aruba"},
                {value: "America/Asuncion", label: "(GMT -04:00) America/Asuncion"},
                {value: "America/Barbados", label: "(GMT -04:00) America/Barbados"},
                {value: "America/Blanc-Sablon", label: "(GMT -04:00) America/Blanc-Sablon"},
                {value: "America/Boa_Vista", label: "(GMT -04:00) America/Boa_Vista"},
                {value: "America/Campo_Grande", label: "(GMT -04:00) America/Campo_Grande"},
                {value: "America/Caracas", label: "(GMT -04:00) America/Caracas"},
                {value: "America/Cuiaba", label: "(GMT -04:00) America/Cuiaba"},
                {value: "America/Curacao", label: "(GMT -04:00) America/Curacao"},
                {value: "America/Detroit", label: "(GMT -04:00) America/Detroit"},
                {value: "America/Dominica", label: "(GMT -04:00) America/Dominica"},
                {value: "America/Fort_Wayne", label: "(GMT -04:00) America/Fort_Wayne"},
                {value: "America/Grand_Turk", label: "(GMT -04:00) America/Grand_Turk"},
                {value: "America/Grenada", label: "(GMT -04:00) America/Grenada"},
                {value: "America/Guadeloupe", label: "(GMT -04:00) America/Guadeloupe"},
                {value: "America/Guyana", label: "(GMT -04:00) America/Guyana"},
                {value: "America/Havana", label: "(GMT -04:00) America/Havana"},
                {value: "America/Indiana/Indianapolis", label: "(GMT -04:00) America/Indiana/Indianapolis"},
                {value: "America/Indiana/Marengo", label: "(GMT -04:00) America/Indiana/Marengo"},
                {value: "America/Indiana/Petersburg", label: "(GMT -04:00) America/Indiana/Petersburg"},
                {value: "America/Indiana/Vevay", label: "(GMT -04:00) America/Indiana/Vevay"},
                {value: "America/Indiana/Vincennes", label: "(GMT -04:00) America/Indiana/Vincennes"},
                {value: "America/Indiana/Winamac", label: "(GMT -04:00) America/Indiana/Winamac"},
                {value: "America/Indianapolis", label: "(GMT -04:00) America/Indianapolis"},
                {value: "America/Iqaluit", label: "(GMT -04:00) America/Iqaluit"},
                {value: "America/Kentucky/Louisville", label: "(GMT -04:00) America/Kentucky/Louisville"},
                {value: "America/Kentucky/Monticello", label: "(GMT -04:00) America/Kentucky/Monticello"},
                {value: "America/Kralendijk", label: "(GMT -04:00) America/Kralendijk"},
                {value: "America/La_Paz", label: "(GMT -04:00) America/La_Paz"},
                {value: "America/Louisville", label: "(GMT -04:00) America/Louisville"},
                {value: "America/Lower_Princes", label: "(GMT -04:00) America/Lower_Princes"},
                {value: "America/Manaus", label: "(GMT -04:00) America/Manaus"},
                {value: "America/Marigot", label: "(GMT -04:00) America/Marigot"},
                {value: "America/Martinique", label: "(GMT -04:00) America/Martinique"},
                {value: "America/Montreal", label: "(GMT -04:00) America/Montreal"},
                {value: "America/Montserrat", label: "(GMT -04:00) America/Montserrat"},
                {value: "America/Nassau", label: "(GMT -04:00) America/Nassau"},
                {value: "America/New_York", label: "(GMT -04:00) America/New_York"},
                {value: "America/Nipigon", label: "(GMT -04:00) America/Nipigon"},
                {value: "America/Pangnirtung", label: "(GMT -04:00) America/Pangnirtung"},
                {value: "America/Port-au-Prince", label: "(GMT -04:00) America/Port-au-Prince"},
                {value: "America/Porto_Velho", label: "(GMT -04:00) America/Porto_Velho"},
                {value: "America/Port_of_Spain", label: "(GMT -04:00) America/Port_of_Spain"},
                {value: "America/Puerto_Rico", label: "(GMT -04:00) America/Puerto_Rico"},
                {value: "America/Santo_Domingo", label: "(GMT -04:00) America/Santo_Domingo"},
                {value: "America/St_Barthelemy", label: "(GMT -04:00) America/St_Barthelemy"},
                {value: "America/St_Kitts", label: "(GMT -04:00) America/St_Kitts"},
                {value: "America/St_Lucia", label: "(GMT -04:00) America/St_Lucia"},
                {value: "America/St_Thomas", label: "(GMT -04:00) America/St_Thomas"},
                {value: "America/St_Vincent", label: "(GMT -04:00) America/St_Vincent"},
                {value: "America/Thunder_Bay", label: "(GMT -04:00) America/Thunder_Bay"},
                {value: "America/Toronto", label: "(GMT -04:00) America/Toronto"},
                {value: "America/Tortola", label: "(GMT -04:00) America/Tortola"},
                {value: "America/Virgin", label: "(GMT -04:00) America/Virgin"},
                {value: "Brazil/West", label: "(GMT -04:00) Brazil/West"},
                {value: "Canada/Eastern", label: "(GMT -04:00) Canada/Eastern"},
                {value: "Cuba", label: "(GMT -04:00) Cuba"},
                {value: "EST5EDT", label: "(GMT -04:00) EST5EDT"},
                {value: "US/East-Indiana", label: "(GMT -04:00) US/East-Indiana"},
                {value: "US/Eastern", label: "(GMT -04:00) US/Eastern"},
                {value: "US/Michigan", label: "(GMT -04:00) US/Michigan"},
                {value: "America/Araguaina", label: "(GMT -03:00) America/Araguaina"},
                {value: "America/Argentina/Buenos_Aires", label: "(GMT -03:00) America/Argentina/Buenos_Aires"},
                {value: "America/Argentina/Catamarca", label: "(GMT -03:00) America/Argentina/Catamarca"},
                {value: "America/Argentina/ComodRivadavia", label: "(GMT -03:00) America/Argentina/ComodRivadavia"},
                {value: "America/Argentina/Cordoba", label: "(GMT -03:00) America/Argentina/Cordoba"},
                {value: "America/Argentina/Jujuy", label: "(GMT -03:00) America/Argentina/Jujuy"},
                {value: "America/Argentina/La_Rioja", label: "(GMT -03:00) America/Argentina/La_Rioja"},
                {value: "America/Argentina/Mendoza", label: "(GMT -03:00) America/Argentina/Mendoza"},
                {value: "America/Argentina/Rio_Gallegos", label: "(GMT -03:00) America/Argentina/Rio_Gallegos"},
                {value: "America/Argentina/Salta", label: "(GMT -03:00) America/Argentina/Salta"},
                {value: "America/Argentina/San_Juan", label: "(GMT -03:00) America/Argentina/San_Juan"},
                {value: "America/Argentina/San_Luis", label: "(GMT -03:00) America/Argentina/San_Luis"},
                {value: "America/Argentina/Tucuman", label: "(GMT -03:00) America/Argentina/Tucuman"},
                {value: "America/Argentina/Ushuaia", label: "(GMT -03:00) America/Argentina/Ushuaia"},
                {value: "America/Bahia", label: "(GMT -03:00) America/Bahia"},
                {value: "America/Belem", label: "(GMT -03:00) America/Belem"},
                {value: "America/Buenos_Aires", label: "(GMT -03:00) America/Buenos_Aires"},
                {value: "America/Catamarca", label: "(GMT -03:00) America/Catamarca"},
                {value: "America/Cayenne", label: "(GMT -03:00) America/Cayenne"},
                {value: "America/Cordoba", label: "(GMT -03:00) America/Cordoba"},
                {value: "America/Fortaleza", label: "(GMT -03:00) America/Fortaleza"},
                {value: "America/Glace_Bay", label: "(GMT -03:00) America/Glace_Bay"},
                {value: "America/Goose_Bay", label: "(GMT -03:00) America/Goose_Bay"},
                {value: "America/Halifax", label: "(GMT -03:00) America/Halifax"},
                {value: "America/Jujuy", label: "(GMT -03:00) America/Jujuy"},
                {value: "America/Maceio", label: "(GMT -03:00) America/Maceio"},
                {value: "America/Mendoza", label: "(GMT -03:00) America/Mendoza"},
                {value: "America/Moncton", label: "(GMT -03:00) America/Moncton"},
                {value: "America/Montevideo", label: "(GMT -03:00) America/Montevideo"},
                {value: "America/Paramaribo", label: "(GMT -03:00) America/Paramaribo"},
                {value: "America/Punta_Arenas", label: "(GMT -03:00) America/Punta_Arenas"},
                {value: "America/Recife", label: "(GMT -03:00) America/Recife"},
                {value: "America/Rosario", label: "(GMT -03:00) America/Rosario"},
                {value: "America/Santarem", label: "(GMT -03:00) America/Santarem"},
                {value: "America/Santiago", label: "(GMT -03:00) America/Santiago"},
                {value: "America/Sao_Paulo", label: "(GMT -03:00) America/Sao_Paulo"},
                {value: "America/Thule", label: "(GMT -03:00) America/Thule"},
                {value: "Antarctica/Palmer", label: "(GMT -03:00) Antarctica/Palmer"},
                {value: "Antarctica/Rothera", label: "(GMT -03:00) Antarctica/Rothera"},
                {value: "Atlantic/Bermuda", label: "(GMT -03:00) Atlantic/Bermuda"},
                {value: "Atlantic/Stanley", label: "(GMT -03:00) Atlantic/Stanley"},
                {value: "Brazil/East", label: "(GMT -03:00) Brazil/East"},
                {value: "Canada/Atlantic", label: "(GMT -03:00) Canada/Atlantic"},
                {value: "Chile/Continental", label: "(GMT -03:00) Chile/Continental"},
                {value: "America/St_Johns", label: "(GMT -02:30) America/St_Johns"},
                {value: "Canada/Newfoundland", label: "(GMT -02:30) Canada/Newfoundland"},
                {value: "America/Godthab", label: "(GMT -02:00) America/Godthab"},
                {value: "America/Miquelon", label: "(GMT -02:00) America/Miquelon"},
                {value: "America/Noronha", label: "(GMT -02:00) America/Noronha"},
                {value: "Atlantic/South_Georgia", label: "(GMT -02:00) Atlantic/South_Georgia"},
                {value: "Brazil/DeNoronha", label: "(GMT -02:00) Brazil/DeNoronha"},
                {value: "Atlantic/Cape_Verde", label: "(GMT -01:00) Atlantic/Cape_Verde"},
                {value: "Africa/Abidjan", label: "(GMT +00:00) Africa/Abidjan"},
                {value: "Africa/Accra", label: "(GMT +00:00) Africa/Accra"},
                {value: "Africa/Bamako", label: "(GMT +00:00) Africa/Bamako"},
                {value: "Africa/Banjul", label: "(GMT +00:00) Africa/Banjul"},
                {value: "Africa/Bissau", label: "(GMT +00:00) Africa/Bissau"},
                {value: "Africa/Conakry", label: "(GMT +00:00) Africa/Conakry"},
                {value: "Africa/Dakar", label: "(GMT +00:00) Africa/Dakar"},
                {value: "Africa/Freetown", label: "(GMT +00:00) Africa/Freetown"},
                {value: "Africa/Lome", label: "(GMT +00:00) Africa/Lome"},
                {value: "Africa/Monrovia", label: "(GMT +00:00) Africa/Monrovia"},
                {value: "Africa/Nouakchott", label: "(GMT +00:00) Africa/Nouakchott"},
                {value: "Africa/Ouagadougou", label: "(GMT +00:00) Africa/Ouagadougou"},
                {value: "Africa/Sao_Tome", label: "(GMT +00:00) Africa/Sao_Tome"},
                {value: "Africa/Timbuktu", label: "(GMT +00:00) Africa/Timbuktu"},
                {value: "America/Danmarkshavn", label: "(GMT +00:00) America/Danmarkshavn"},
                {value: "America/Scoresbysund", label: "(GMT +00:00) America/Scoresbysund"},
                {value: "Atlantic/Azores", label: "(GMT +00:00) Atlantic/Azores"},
                {value: "Atlantic/Reykjavik", label: "(GMT +00:00) Atlantic/Reykjavik"},
                {value: "Atlantic/St_Helena", label: "(GMT +00:00) Atlantic/St_Helena"},
                {value: "GMT", label: "(GMT +00:00) GMT"},
                {value: "Greenwich", label: "(GMT +00:00) Greenwich"},
                {value: "Iceland", label: "(GMT +00:00) Iceland"},
                {value: "UCT", label: "(GMT +00:00) UCT"},
                {value: "Universal", label: "(GMT +00:00) Universal"},
                {value: "UTC", label: "(GMT +00:00) UTC"},
                {value: "Zulu", label: "(GMT +00:00) Zulu"},
                {value: "Africa/Algiers", label: "(GMT +01:00) Africa/Algiers"},
                {value: "Africa/Bangui", label: "(GMT +01:00) Africa/Bangui"},
                {value: "Africa/Brazzaville", label: "(GMT +01:00) Africa/Brazzaville"},
                {value: "Africa/Casablanca", label: "(GMT +01:00) Africa/Casablanca"},
                {value: "Africa/Douala", label: "(GMT +01:00) Africa/Douala"},
                {value: "Africa/El_Aaiun", label: "(GMT +01:00) Africa/El_Aaiun"},
                {value: "Africa/Kinshasa", label: "(GMT +01:00) Africa/Kinshasa"},
                {value: "Africa/Lagos", label: "(GMT +01:00) Africa/Lagos"},
                {value: "Africa/Libreville", label: "(GMT +01:00) Africa/Libreville"},
                {value: "Africa/Luanda", label: "(GMT +01:00) Africa/Luanda"},
                {value: "Africa/Malabo", label: "(GMT +01:00) Africa/Malabo"},
                {value: "Africa/Ndjamena", label: "(GMT +01:00) Africa/Ndjamena"},
                {value: "Africa/Niamey", label: "(GMT +01:00) Africa/Niamey"},
                {value: "Africa/Porto-Novo", label: "(GMT +01:00) Africa/Porto-Novo"},
                {value: "Africa/Tunis", label: "(GMT +01:00) Africa/Tunis"},
                {value: "Atlantic/Canary", label: "(GMT +01:00) Atlantic/Canary"},
                {value: "Atlantic/Faeroe", label: "(GMT +01:00) Atlantic/Faeroe"},
                {value: "Atlantic/Faroe", label: "(GMT +01:00) Atlantic/Faroe"},
                {value: "Atlantic/Madeira", label: "(GMT +01:00) Atlantic/Madeira"},
                {value: "Eire", label: "(GMT +01:00) Eire"},
                {value: "Europe/Belfast", label: "(GMT +01:00) Europe/Belfast"},
                {value: "Europe/Dublin", label: "(GMT +01:00) Europe/Dublin"},
                {value: "Europe/Guernsey", label: "(GMT +01:00) Europe/Guernsey"},
                {value: "Europe/Isle_of_Man", label: "(GMT +01:00) Europe/Isle_of_Man"},
                {value: "Europe/Jersey", label: "(GMT +01:00) Europe/Jersey"},
                {value: "Europe/Lisbon", label: "(GMT +01:00) Europe/Lisbon"},
                {value: "Europe/London", label: "(GMT +01:00) Europe/London"},
                {value: "GB", label: "(GMT +01:00) GB"},
                {value: "GB-Eire", label: "(GMT +01:00) GB-Eire"},
                {value: "Portugal", label: "(GMT +01:00) Portugal"},
                {value: "WET", label: "(GMT +01:00) WET"},
                {value: "Africa/Blantyre", label: "(GMT +02:00) Africa/Blantyre"},
                {value: "Africa/Bujumbura", label: "(GMT +02:00) Africa/Bujumbura"},
                {value: "Africa/Cairo", label: "(GMT +02:00) Africa/Cairo"},
                {value: "Africa/Ceuta", label: "(GMT +02:00) Africa/Ceuta"},
                {value: "Africa/Gaborone", label: "(GMT +02:00) Africa/Gaborone"},
                {value: "Africa/Harare", label: "(GMT +02:00) Africa/Harare"},
                {value: "Africa/Johannesburg", label: "(GMT +02:00) Africa/Johannesburg"},
                {value: "Africa/Khartoum", label: "(GMT +02:00) Africa/Khartoum"},
                {value: "Africa/Kigali", label: "(GMT +02:00) Africa/Kigali"},
                {value: "Africa/Lubumbashi", label: "(GMT +02:00) Africa/Lubumbashi"},
                {value: "Africa/Lusaka", label: "(GMT +02:00) Africa/Lusaka"},
                {value: "Africa/Maputo", label: "(GMT +02:00) Africa/Maputo"},
                {value: "Africa/Maseru", label: "(GMT +02:00) Africa/Maseru"},
                {value: "Africa/Mbabane", label: "(GMT +02:00) Africa/Mbabane"},
                {value: "Africa/Tripoli", label: "(GMT +02:00) Africa/Tripoli"},
                {value: "Africa/Windhoek", label: "(GMT +02:00) Africa/Windhoek"},
                {value: "Antarctica/Troll", label: "(GMT +02:00) Antarctica/Troll"},
                {value: "Arctic/Longyearbyen", label: "(GMT +02:00) Arctic/Longyearbyen"},
                {value: "Atlantic/Jan_Mayen", label: "(GMT +02:00) Atlantic/Jan_Mayen"},
                {value: "CET", label: "(GMT +02:00) CET"},
                {value: "Egypt", label: "(GMT +02:00) Egypt"},
                {value: "Europe/Amsterdam", label: "(GMT +02:00) Europe/Amsterdam"},
                {value: "Europe/Andorra", label: "(GMT +02:00) Europe/Andorra"},
                {value: "Europe/Belgrade", label: "(GMT +02:00) Europe/Belgrade"},
                {value: "Europe/Berlin", label: "(GMT +02:00) Europe/Berlin"},
                {value: "Europe/Bratislava", label: "(GMT +02:00) Europe/Bratislava"},
                {value: "Europe/Brussels", label: "(GMT +02:00) Europe/Brussels"},
                {value: "Europe/Budapest", label: "(GMT +02:00) Europe/Budapest"},
                {value: "Europe/Busingen", label: "(GMT +02:00) Europe/Busingen"},
                {value: "Europe/Copenhagen", label: "(GMT +02:00) Europe/Copenhagen"},
                {value: "Europe/Gibraltar", label: "(GMT +02:00) Europe/Gibraltar"},
                {value: "Europe/Kaliningrad", label: "(GMT +02:00) Europe/Kaliningrad"},
                {value: "Europe/Ljubljana", label: "(GMT +02:00) Europe/Ljubljana"},
                {value: "Europe/Luxembourg", label: "(GMT +02:00) Europe/Luxembourg"},
                {value: "Europe/Madrid", label: "(GMT +02:00) Europe/Madrid"},
                {value: "Europe/Malta", label: "(GMT +02:00) Europe/Malta"},
                {value: "Europe/Monaco", label: "(GMT +02:00) Europe/Monaco"},
                {value: "Europe/Oslo", label: "(GMT +02:00) Europe/Oslo"},
                {value: "Europe/Paris", label: "(GMT +02:00) Europe/Paris"},
                {value: "Europe/Podgorica", label: "(GMT +02:00) Europe/Podgorica"},
                {value: "Europe/Prague", label: "(GMT +02:00) Europe/Prague"},
                {value: "Europe/Rome", label: "(GMT +02:00) Europe/Rome"},
                {value: "Europe/San_Marino", label: "(GMT +02:00) Europe/San_Marino"},
                {value: "Europe/Sarajevo", label: "(GMT +02:00) Europe/Sarajevo"},
                {value: "Europe/Skopje", label: "(GMT +02:00) Europe/Skopje"},
                {value: "Europe/Stockholm", label: "(GMT +02:00) Europe/Stockholm"},
                {value: "Europe/Tirane", label: "(GMT +02:00) Europe/Tirane"},
                {value: "Europe/Vaduz", label: "(GMT +02:00) Europe/Vaduz"},
                {value: "Europe/Vatican", label: "(GMT +02:00) Europe/Vatican"},
                {value: "Europe/Vienna", label: "(GMT +02:00) Europe/Vienna"},
                {value: "Europe/Warsaw", label: "(GMT +02:00) Europe/Warsaw"},
                {value: "Europe/Zagreb", label: "(GMT +02:00) Europe/Zagreb"},
                {value: "Europe/Zurich", label: "(GMT +02:00) Europe/Zurich"},
                {value: "Libya", label: "(GMT +02:00) Libya"},
                {value: "MET", label: "(GMT +02:00) MET"},
                {value: "Poland", label: "(GMT +02:00) Poland"},
                {value: "Africa/Addis_Ababa", label: "(GMT +03:00) Africa/Addis_Ababa"},
                {value: "Africa/Asmara", label: "(GMT +03:00) Africa/Asmara"},
                {value: "Africa/Asmera", label: "(GMT +03:00) Africa/Asmera"},
                {value: "Africa/Dar_es_Salaam", label: "(GMT +03:00) Africa/Dar_es_Salaam"},
                {value: "Africa/Djibouti", label: "(GMT +03:00) Africa/Djibouti"},
                {value: "Africa/Juba", label: "(GMT +03:00) Africa/Juba"},
                {value: "Africa/Kampala", label: "(GMT +03:00) Africa/Kampala"},
                {value: "Africa/Mogadishu", label: "(GMT +03:00) Africa/Mogadishu"},
                {value: "Africa/Nairobi", label: "(GMT +03:00) Africa/Nairobi"},
                {value: "Antarctica/Syowa", label: "(GMT +03:00) Antarctica/Syowa"},
                {value: "Asia/Aden", label: "(GMT +03:00) Asia/Aden"},
                {value: "Asia/Amman", label: "(GMT +03:00) Asia/Amman"},
                {value: "Asia/Baghdad", label: "(GMT +03:00) Asia/Baghdad"},
                {value: "Asia/Bahrain", label: "(GMT +03:00) Asia/Bahrain"},
                {value: "Asia/Beirut", label: "(GMT +03:00) Asia/Beirut"},
                {value: "Asia/Damascus", label: "(GMT +03:00) Asia/Damascus"},
                {value: "Asia/Famagusta", label: "(GMT +03:00) Asia/Famagusta"},
                {value: "Asia/Gaza", label: "(GMT +03:00) Asia/Gaza"},
                {value: "Asia/Hebron", label: "(GMT +03:00) Asia/Hebron"},
                {value: "Asia/Istanbul", label: "(GMT +03:00) Asia/Istanbul"},
                {value: "Asia/Jerusalem", label: "(GMT +03:00) Asia/Jerusalem"},
                {value: "Asia/Kuwait", label: "(GMT +03:00) Asia/Kuwait"},
                {value: "Asia/Nicosia", label: "(GMT +03:00) Asia/Nicosia"},
                {value: "Asia/Qatar", label: "(GMT +03:00) Asia/Qatar"},
                {value: "Asia/Riyadh", label: "(GMT +03:00) Asia/Riyadh"},
                {value: "Asia/Tel_Aviv", label: "(GMT +03:00) Asia/Tel_Aviv"},
                {value: "EET", label: "(GMT +03:00) EET"},
                {value: "Europe/Athens", label: "(GMT +03:00) Europe/Athens"},
                {value: "Europe/Bucharest", label: "(GMT +03:00) Europe/Bucharest"},
                {value: "Europe/Chisinau", label: "(GMT +03:00) Europe/Chisinau"},
                {value: "Europe/Helsinki", label: "(GMT +03:00) Europe/Helsinki"},
                {value: "Europe/Istanbul", label: "(GMT +03:00) Europe/Istanbul"},
                {value: "Europe/Kiev", label: "(GMT +03:00) Europe/Kiev"},
                {value: "Europe/Kirov", label: "(GMT +03:00) Europe/Kirov"},
                {value: "Europe/Mariehamn", label: "(GMT +03:00) Europe/Mariehamn"},
                {value: "Europe/Minsk", label: "(GMT +03:00) Europe/Minsk"},
                {value: "Europe/Moscow", label: "(GMT +03:00) Europe/Moscow"},
                {value: "Europe/Nicosia", label: "(GMT +03:00) Europe/Nicosia"},
                {value: "Europe/Riga", label: "(GMT +03:00) Europe/Riga"},
                {value: "Europe/Simferopol", label: "(GMT +03:00) Europe/Simferopol"},
                {value: "Europe/Sofia", label: "(GMT +03:00) Europe/Sofia"},
                {value: "Europe/Tallinn", label: "(GMT +03:00) Europe/Tallinn"},
                {value: "Europe/Tiraspol", label: "(GMT +03:00) Europe/Tiraspol"},
                {value: "Europe/Uzhgorod", label: "(GMT +03:00) Europe/Uzhgorod"},
                {value: "Europe/Vilnius", label: "(GMT +03:00) Europe/Vilnius"},
                {value: "Europe/Volgograd", label: "(GMT +03:00) Europe/Volgograd"},
                {value: "Europe/Zaporozhye", label: "(GMT +03:00) Europe/Zaporozhye"},
                {value: "Indian/Antananarivo", label: "(GMT +03:00) Indian/Antananarivo"},
                {value: "Indian/Comoro", label: "(GMT +03:00) Indian/Comoro"},
                {value: "Indian/Mayotte", label: "(GMT +03:00) Indian/Mayotte"},
                {value: "Israel", label: "(GMT +03:00) Israel"},
                {value: "Turkey", label: "(GMT +03:00) Turkey"},
                {value: "W-SU", label: "(GMT +03:00) W-SU"},
                {value: "Asia/Tehran", label: "(GMT +03:30) Asia/Tehran"},
                {value: "Iran", label: "(GMT +03:30) Iran"},
                {value: "Asia/Baku", label: "(GMT +04:00) Asia/Baku"},
                {value: "Asia/Dubai", label: "(GMT +04:00) Asia/Dubai"},
                {value: "Asia/Muscat", label: "(GMT +04:00) Asia/Muscat"},
                {value: "Asia/Tbilisi", label: "(GMT +04:00) Asia/Tbilisi"},
                {value: "Asia/Yerevan", label: "(GMT +04:00) Asia/Yerevan"},
                {value: "Europe/Astrakhan", label: "(GMT +04:00) Europe/Astrakhan"},
                {value: "Europe/Samara", label: "(GMT +04:00) Europe/Samara"},
                {value: "Europe/Saratov", label: "(GMT +04:00) Europe/Saratov"},
                {value: "Europe/Ulyanovsk", label: "(GMT +04:00) Europe/Ulyanovsk"},
                {value: "Indian/Mahe", label: "(GMT +04:00) Indian/Mahe"},
                {value: "Indian/Mauritius", label: "(GMT +04:00) Indian/Mauritius"},
                {value: "Indian/Reunion", label: "(GMT +04:00) Indian/Reunion"},
                {value: "Asia/Kabul", label: "(GMT +04:30) Asia/Kabul"},
                {value: "Antarctica/Mawson", label: "(GMT +05:00) Antarctica/Mawson"},
                {value: "Asia/Aqtau", label: "(GMT +05:00) Asia/Aqtau"},
                {value: "Asia/Aqtobe", label: "(GMT +05:00) Asia/Aqtobe"},
                {value: "Asia/Ashgabat", label: "(GMT +05:00) Asia/Ashgabat"},
                {value: "Asia/Ashkhabad", label: "(GMT +05:00) Asia/Ashkhabad"},
                {value: "Asia/Atyrau", label: "(GMT +05:00) Asia/Atyrau"},
                {value: "Asia/Dushanbe", label: "(GMT +05:00) Asia/Dushanbe"},
                {value: "Asia/Karachi", label: "(GMT +05:00) Asia/Karachi"},
                {value: "Asia/Oral", label: "(GMT +05:00) Asia/Oral"},
                {value: "Asia/Samarkand", label: "(GMT +05:00) Asia/Samarkand"},
                {value: "Asia/Tashkent", label: "(GMT +05:00) Asia/Tashkent"},
                {value: "Asia/Yekaterinburg", label: "(GMT +05:00) Asia/Yekaterinburg"},
                {value: "Indian/Kerguelen", label: "(GMT +05:00) Indian/Kerguelen"},
                {value: "Indian/Maldives", label: "(GMT +05:00) Indian/Maldives"},
                {value: "Asia/Calcutta", label: "(GMT +05:30) Asia/Calcutta"},
                {value: "Asia/Colombo", label: "(GMT +05:30) Asia/Colombo"},
                {value: "Asia/Kolkata", label: "(GMT +05:30) Asia/Kolkata"},
                {value: "Asia/Kathmandu", label: "(GMT +05:45) Asia/Kathmandu"},
                {value: "Asia/Katmandu", label: "(GMT +05:45) Asia/Katmandu"},
                {value: "Antarctica/Vostok", label: "(GMT +06:00) Antarctica/Vostok"},
                {value: "Asia/Almaty", label: "(GMT +06:00) Asia/Almaty"},
                {value: "Asia/Bishkek", label: "(GMT +06:00) Asia/Bishkek"},
                {value: "Asia/Dacca", label: "(GMT +06:00) Asia/Dacca"},
                {value: "Asia/Dhaka", label: "(GMT +06:00) Asia/Dhaka"},
                {value: "Asia/Kashgar", label: "(GMT +06:00) Asia/Kashgar"},
                {value: "Asia/Omsk", label: "(GMT +06:00) Asia/Omsk"},
                {value: "Asia/Qyzylorda", label: "(GMT +06:00) Asia/Qyzylorda"},
                {value: "Asia/Thimbu", label: "(GMT +06:00) Asia/Thimbu"},
                {value: "Asia/Thimphu", label: "(GMT +06:00) Asia/Thimphu"},
                {value: "Asia/Urumqi", label: "(GMT +06:00) Asia/Urumqi"},
                {value: "Indian/Chagos", label: "(GMT +06:00) Indian/Chagos"},
                {value: "Asia/Rangoon", label: "(GMT +06:30) Asia/Rangoon"},
                {value: "Asia/Yangon", label: "(GMT +06:30) Asia/Yangon"},
                {value: "Indian/Cocos", label: "(GMT +06:30) Indian/Cocos"},
                {value: "Antarctica/Davis", label: "(GMT +07:00) Antarctica/Davis"},
                {value: "Asia/Bangkok", label: "(GMT +07:00) Asia/Bangkok"},
                {value: "Asia/Barnaul", label: "(GMT +07:00) Asia/Barnaul"},
                {value: "Asia/Hovd", label: "(GMT +07:00) Asia/Hovd"},
                {value: "Asia/Ho_Chi_Minh", label: "(GMT +07:00) Asia/Ho_Chi_Minh"},
                {value: "Asia/Jakarta", label: "(GMT +07:00) Asia/Jakarta"},
                {value: "Asia/Krasnoyarsk", label: "(GMT +07:00) Asia/Krasnoyarsk"},
                {value: "Asia/Novokuznetsk", label: "(GMT +07:00) Asia/Novokuznetsk"},
                {value: "Asia/Novosibirsk", label: "(GMT +07:00) Asia/Novosibirsk"},
                {value: "Asia/Phnom_Penh", label: "(GMT +07:00) Asia/Phnom_Penh"},
                {value: "Asia/Pontianak", label: "(GMT +07:00) Asia/Pontianak"},
                {value: "Asia/Saigon", label: "(GMT +07:00) Asia/Saigon"},
                {value: "Asia/Tomsk", label: "(GMT +07:00) Asia/Tomsk"},
                {value: "Asia/Vientiane", label: "(GMT +07:00) Asia/Vientiane"},
                {value: "Indian/Christmas", label: "(GMT +07:00) Indian/Christmas"},
                {value: "Asia/Brunei", label: "(GMT +08:00) Asia/Brunei"},
                {value: "Asia/Choibalsan", label: "(GMT +08:00) Asia/Choibalsan"},
                {value: "Asia/Chongqing", label: "(GMT +08:00) Asia/Chongqing"},
                {value: "Asia/Chungking", label: "(GMT +08:00) Asia/Chungking"},
                {value: "Asia/Harbin", label: "(GMT +08:00) Asia/Harbin"},
                {value: "Asia/Hong_Kong", label: "(GMT +08:00) Asia/Hong_Kong"},
                {value: "Asia/Irkutsk", label: "(GMT +08:00) Asia/Irkutsk"},
                {value: "Asia/Kuala_Lumpur", label: "(GMT +08:00) Asia/Kuala_Lumpur"},
                {value: "Asia/Kuching", label: "(GMT +08:00) Asia/Kuching"},
                {value: "Asia/Macao", label: "(GMT +08:00) Asia/Macao"},
                {value: "Asia/Macau", label: "(GMT +08:00) Asia/Macau"},
                {value: "Asia/Makassar", label: "(GMT +08:00) Asia/Makassar"},
                {value: "Asia/Manila", label: "(GMT +08:00) Asia/Manila"},
                {value: "Asia/Shanghai", label: "(GMT +08:00) Asia/Shanghai"},
                {value: "Asia/Singapore", label: "(GMT +08:00) Asia/Singapore"},
                {value: "Asia/Taipei", label: "(GMT +08:00) Asia/Taipei"},
                {value: "Asia/Ujung_Pandang", label: "(GMT +08:00) Asia/Ujung_Pandang"},
                {value: "Asia/Ulaanbaatar", label: "(GMT +08:00) Asia/Ulaanbaatar"},
                {value: "Asia/Ulan_Bator", label: "(GMT +08:00) Asia/Ulan_Bator"},
                {value: "Australia/Perth", label: "(GMT +08:00) Australia/Perth"},
                {value: "Australia/West", label: "(GMT +08:00) Australia/West"},
                {value: "Hongkong", label: "(GMT +08:00) Hongkong"},
                {value: "PRC", label: "(GMT +08:00) PRC"},
                {value: "ROC", label: "(GMT +08:00) ROC"},
                {value: "Singapore", label: "(GMT +08:00) Singapore"},
                {value: "Asia/Pyongyang", label: "(GMT +08:30) Asia/Pyongyang"},
                {value: "Australia/Eucla", label: "(GMT +08:45) Australia/Eucla"},
                {value: "Asia/Chita", label: "(GMT +09:00) Asia/Chita"},
                {value: "Asia/Dili", label: "(GMT +09:00) Asia/Dili"},
                {value: "Asia/Jayapura", label: "(GMT +09:00) Asia/Jayapura"},
                {value: "Asia/Khandyga", label: "(GMT +09:00) Asia/Khandyga"},
                {value: "Asia/Seoul", label: "(GMT +09:00) Asia/Seoul"},
                {value: "Asia/Tokyo", label: "(GMT +09:00) Asia/Tokyo"},
                {value: "Asia/Yakutsk", label: "(GMT +09:00) Asia/Yakutsk"},
                {value: "Japan", label: "(GMT +09:00) Japan"},
                {value: "Pacific/Palau", label: "(GMT +09:00) Pacific/Palau"},
                {value: "ROK", label: "(GMT +09:00) ROK"},
                {value: "Australia/Adelaide", label: "(GMT +09:30) Australia/Adelaide"},
                {value: "Australia/Broken_Hill", label: "(GMT +09:30) Australia/Broken_Hill"},
                {value: "Australia/Darwin", label: "(GMT +09:30) Australia/Darwin"},
                {value: "Australia/North", label: "(GMT +09:30) Australia/North"},
                {value: "Australia/South", label: "(GMT +09:30) Australia/South"},
                {value: "Australia/Yancowinna", label: "(GMT +09:30) Australia/Yancowinna"},
                {value: "Antarctica/DumontDUrville", label: "(GMT +10:00) Antarctica/DumontDUrville"},
                {value: "Asia/Ust-Nera", label: "(GMT +10:00) Asia/Ust-Nera"},
                {value: "Asia/Vladivostok", label: "(GMT +10:00) Asia/Vladivostok"},
                {value: "Australia/ACT", label: "(GMT +10:00) Australia/ACT"},
                {value: "Australia/Brisbane", label: "(GMT +10:00) Australia/Brisbane"},
                {value: "Australia/Canberra", label: "(GMT +10:00) Australia/Canberra"},
                {value: "Australia/Currie", label: "(GMT +10:00) Australia/Currie"},
                {value: "Australia/Hobart", label: "(GMT +10:00) Australia/Hobart"},
                {value: "Australia/Lindeman", label: "(GMT +10:00) Australia/Lindeman"},
                {value: "Australia/Melbourne", label: "(GMT +10:00) Australia/Melbourne"},
                {value: "Australia/NSW", label: "(GMT +10:00) Australia/NSW"},
                {value: "Australia/Queensland", label: "(GMT +10:00) Australia/Queensland"},
                {value: "Australia/Sydney", label: "(GMT +10:00) Australia/Sydney"},
                {value: "Australia/Tasmania", label: "(GMT +10:00) Australia/Tasmania"},
                {value: "Australia/Victoria", label: "(GMT +10:00) Australia/Victoria"},
                {value: "Pacific/Chuuk", label: "(GMT +10:00) Pacific/Chuuk"},
                {value: "Pacific/Guam", label: "(GMT +10:00) Pacific/Guam"},
                {value: "Pacific/Port_Moresby", label: "(GMT +10:00) Pacific/Port_Moresby"},
                {value: "Pacific/Saipan", label: "(GMT +10:00) Pacific/Saipan"},
                {value: "Pacific/Truk", label: "(GMT +10:00) Pacific/Truk"},
                {value: "Pacific/Yap", label: "(GMT +10:00) Pacific/Yap"},
                {value: "Australia/LHI", label: "(GMT +10:30) Australia/LHI"},
                {value: "Australia/Lord_Howe", label: "(GMT +10:30) Australia/Lord_Howe"},
                {value: "Antarctica/Casey", label: "(GMT +11:00) Antarctica/Casey"},
                {value: "Antarctica/Macquarie", label: "(GMT +11:00) Antarctica/Macquarie"},
                {value: "Asia/Magadan", label: "(GMT +11:00) Asia/Magadan"},
                {value: "Asia/Sakhalin", label: "(GMT +11:00) Asia/Sakhalin"},
                {value: "Asia/Srednekolymsk", label: "(GMT +11:00) Asia/Srednekolymsk"},
                {value: "Pacific/Bougainville", label: "(GMT +11:00) Pacific/Bougainville"},
                {value: "Pacific/Efate", label: "(GMT +11:00) Pacific/Efate"},
                {value: "Pacific/Guadalcanal", label: "(GMT +11:00) Pacific/Guadalcanal"},
                {value: "Pacific/Kosrae", label: "(GMT +11:00) Pacific/Kosrae"},
                {value: "Pacific/Norfolk", label: "(GMT +11:00) Pacific/Norfolk"},
                {value: "Pacific/Noumea", label: "(GMT +11:00) Pacific/Noumea"},
                {value: "Pacific/Pohnpei", label: "(GMT +11:00) Pacific/Pohnpei"},
                {value: "Pacific/Ponape", label: "(GMT +11:00) Pacific/Ponape"},
                {value: "Antarctica/McMurdo", label: "(GMT +12:00) Antarctica/McMurdo"},
                {value: "Antarctica/South_Pole", label: "(GMT +12:00) Antarctica/South_Pole"},
                {value: "Asia/Anadyr", label: "(GMT +12:00) Asia/Anadyr"},
                {value: "Asia/Kamchatka", label: "(GMT +12:00) Asia/Kamchatka"},
                {value: "Kwajalein", label: "(GMT +12:00) Kwajalein"},
                {value: "NZ", label: "(GMT +12:00) NZ"},
                {value: "Pacific/Auckland", label: "(GMT +12:00) Pacific/Auckland"},
                {value: "Pacific/Fiji", label: "(GMT +12:00) Pacific/Fiji"},
                {value: "Pacific/Funafuti", label: "(GMT +12:00) Pacific/Funafuti"},
                {value: "Pacific/Kwajalein", label: "(GMT +12:00) Pacific/Kwajalein"},
                {value: "Pacific/Majuro", label: "(GMT +12:00) Pacific/Majuro"},
                {value: "Pacific/Nauru", label: "(GMT +12:00) Pacific/Nauru"},
                {value: "Pacific/Tarawa", label: "(GMT +12:00) Pacific/Tarawa"},
                {value: "Pacific/Wake", label: "(GMT +12:00) Pacific/Wake"},
                {value: "Pacific/Wallis", label: "(GMT +12:00) Pacific/Wallis"},
                {value: "NZ-CHAT", label: "(GMT +12:45) NZ-CHAT"},
                {value: "Pacific/Chatham", label: "(GMT +12:45) Pacific/Chatham"},
                {value: "Pacific/Apia", label: "(GMT +13:00) Pacific/Apia"},
                {value: "Pacific/Enderbury", label: "(GMT +13:00) Pacific/Enderbury"},
                {value: "Pacific/Fakaofo", label: "(GMT +13:00) Pacific/Fakaofo"},
                {value: "Pacific/Tongatapu", label: "(GMT +13:00) Pacific/Tongatapu"},
                {value: "Pacific/Kiritimati", label: "(GMT +14:00) Pacific/Kiritimati"}
            ]
        };
    },

    created () {
    	this.getSettings();

        this.getPlans();

        // Set validation custom message
        const dictionary = {
            en: {
                attributes: {
                    'app.name': 'website name',
                    'app.url': 'website URL',
                    'settings.support_email': 'support email address',
                }
            }
        };
        
        this.$validator.localize(dictionary);
    },

    methods: {
        getPlans() {
            this.showPlaceholder = true;

            axios.get('admin/plans')
            .then(response => {

                this.plans = response.data;

                // check if there is a plan set to auto assign
                let currentPlan = response.data.find( function( ele ) { 
                    return ele.auto_assign === 1;
                });

                if (currentPlan) {
                    this.planAutoAssign.enable = true;
                    this.planAutoAssign.current_plan = currentPlan.id;
                }

            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            }); 
        },

	    getSettings() {
			axios.get('admin/settings')
	        .then(response => {
	        	this.settingsData = response.data;
	        })
	        .catch(error => { 
                this.$alert.error('Something went wrong while processing your request, please refresh the page.');
            });
    	},

        updateSettings() {
    		this.$validator.validateAll('form-general').then((success) => {

                // Return if validation failed
                if ( ! success )
                    return

                this.btnLoadingSettings = true;

	    		axios.post('admin/settings', this.settingsData)
	            .then(response => {

	            	this.btnLoadingSettings = false;
                    this.$alert.success(response.data.message);

	            })
	            .catch(error => {

	            	this.btnLoadingSettings = false;

                    this.$backendErrors(error.response.data, 'form-general');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

	            });

            });
        },

        setOnboarding(planId) {
            axios.put('admin/settings/plan-onboarding/' + planId, this.planAutoAssign)
            .then(response => {
                this.$alert.success(response.data.message);
            })
            .catch(error => {
                this.$alert.error(error.response.data.message);
            });

        },

        disableOnboarding() {
            if ( this.planAutoAssign.enable === false && this.planAutoAssign.current_plan != null ) {
                this.setOnboarding(this.planAutoAssign.current_plan);

                this.planAutoAssign.current_plan = null;
            }
        }

    }
}
</script>