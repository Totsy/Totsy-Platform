
    <h2>Need Help?</h2>
    <hr>

    <ul data-role="listview" data-inset="true">
        <?php if (empty($userInfo)){ ?>
        <li><a href="#" onclick="window.location.href='/pages/contact';return false;">Support</a></li>
        <?php } else { ?>
         <li><a href="#" onclick="window.location.href='/tickets/add';return false;">Support</a></li>
         <?php } ?>

        <li><a href="#" onclick="window.location.href='/pages/faq';return false;">FAQ's</a></li>

        <li><a href="#" onclick="window.location.href='/pages/privacy';return false;">Privacy Policy</a></li>

        <li><a href="#" onclick="window.location.href='/pages/terms';return false;">Terms of Use</a></li>
    </ul>
