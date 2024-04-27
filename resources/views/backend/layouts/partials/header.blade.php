<!-- header area start -->
<div class="header-area">
    <div class="row align-items-center">
        <!-- nav and search button -->
        <div class="col-md-6 col-sm-8 clearfix">
         <h2 class="pull-left" style="color:red;"><marquee> AKIWACU SOFT <span style="color: pink;">| </span><strong style="color: black;">{{ Auth::guard('admin')->user()->name }}</strong></marquee></h2>
        </div>
        <div class="col-md-6 col-sm-4 clearfix">
            <ul class="notification-area pull-right">
                <li id="full-view"><i class="ti-fullscreen"></i></li>
                <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                <li>
                    <select class="form-control changeLang">
                            <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>@lang('messages.english')</option>

                        <option value="ki" {{ session()->get('locale') == 'ki' ? 'selected' : '' }}>@lang('messages.kirundi')</option>

                        <option value="fr" {{ session()->get('locale') == 'fr' ? 'selected' : '' }}>@lang('messages.french')</option>
                </select>
                </li>
                <li class="dropdown">
                    <i class="ti-bell dropdown-toggle" data-toggle="dropdown">
                        <span>1</span>
                    </i>
                    <div class="dropdown-menu bell-notify-box notify-box">
                        <span class="notify-title">vous avez 1 notification</span>
                        <div class="nofity-list">
                            <a href="#" class="notify-item">
                                <div class="notify-thumb"><i class="ti-key btn-danger"></i></div>
                                <div class="notify-text">
                                    <p>mise à jour des permissions</p>
                                    <span>maintenant</span>
                                </div>
                            </a>
                        </div>
                    </div> 

                </li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var url = "{{ route('changeLang')}}";
    $(".changeLang").change(function(){
        window.location.href = url + "?lang=" + $(this).val();
    });
</script>
<!-- header area end -->