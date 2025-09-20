<!DOCTYPE html>
<html lang="en">
@include('business.layouts.header')

<body>
    <div id="db-wrapper">
        <!-- navbar vertical -->
        <!-- Sidebar -->

            @include('business.layouts.nav')

        <!-- Page Content -->
        <main id="page-content">
            <div class="header">
                @include('business.layouts.topbar')
            </div>
            <!-- Container fluid -->

            @yield('content')
        </main>
    </div>
    <!-- Scripts -->
    @include('business.layouts.footer')

    @yield('customjs')


     <!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/667039999a809f19fb3ea12a/1i0j6207u';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->

</body>

</html>
