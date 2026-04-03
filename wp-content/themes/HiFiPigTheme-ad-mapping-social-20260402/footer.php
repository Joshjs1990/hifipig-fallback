<footer class="site-footer">
  <p>© <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
</footer>

<div class="notice-stack" data-notice-stack>
  <div class="subscribe-notice notice-card is-hidden" role="dialog" aria-live="polite" aria-label="Subscribe" data-subscribe-notice>
    <p class="cookie-notice__title">Subscribe</p>
    <p class="cookie-notice__text"><strong>Thanks for visiting.</strong> Stay up to date with Hifi Pig's newsletter.</p>
    <form
      class="subscribe-notice__form"
      action="https://hifipig.us3.list-manage.com/subscribe/post?u=5dc87d0c48b2fa1cfc7f3b168&id=8326f3b4d5"
      accept-charset="UTF-8"
      method="post"
      enctype="multipart/form-data"
      novalidate
      target="_blank"
    >
      <input type="hidden" name="u" value="5dc87d0c48b2fa1cfc7f3b168">
      <input type="hidden" name="id" value="8326f3b4d5">

      <div class="subscribe-notice__honeypot field-shift" aria-hidden="true">
        <label for="b_name">Name</label>
        <input type="text" name="b_name" tabindex="-1" value="" id="b_name">
        <label for="b_email">Email</label>
        <input type="email" name="b_email" tabindex="-1" value="" id="b_email">
        <label for="b_comment">Comment</label>
        <textarea name="b_comment" tabindex="-1" id="b_comment"></textarea>
      </div>

      <div class="subscribe-notice__field">
        <label for="MERGE0">Email Address <span aria-hidden="true">*</span></label>
        <input class="subscribe-notice__input" type="email" name="MERGE0" id="MERGE0" required>
      </div>
      <div class="subscribe-notice__field">
        <label for="MERGE1">First Name</label>
        <input class="subscribe-notice__input" type="text" name="MERGE1" id="MERGE1">
      </div>
      <div class="subscribe-notice__field">
        <label for="MERGE2">Last Name</label>
        <input class="subscribe-notice__input" type="text" name="MERGE2" id="MERGE2">
      </div>

      <div class="subscribe-notice__gdpr">
        <label class="subscribe-notice__label">Marketing Permissions <span aria-hidden="true">*</span></label>
        <p class="subscribe-notice__legal">Please select all the ways you would like to hear from us:</p>
        <div class="subscribe-notice__checkboxes">
          <label class="subscribe-notice__checkbox" for="gdpr_577">
            <input type="checkbox" id="gdpr_577" name="gdpr[577]" value="Y">
            <span>Email</span>
          </label>
          <label class="subscribe-notice__checkbox" for="gdpr_581">
            <input type="checkbox" id="gdpr_581" name="gdpr[581]" value="Y">
            <span>Direct Mail</span>
          </label>
          <label class="subscribe-notice__checkbox" for="gdpr_585">
            <input type="checkbox" id="gdpr_585" name="gdpr[585]" value="Y">
            <span>Customized online advertising</span>
          </label>
        </div>
        <p class="subscribe-notice__legal">You can change your mind at any time by clicking the unsubscribe link in the footer of any email you receive from us, or by contacting us at stuart@hifipig.com. We will treat your information with respect.</p>
        <p class="subscribe-notice__legal">We use Mailchimp as our marketing platform. By clicking below to subscribe, you acknowledge that your information will be transferred to Mailchimp for processing. <a href="https://mailchimp.com/legal/terms" target="_blank" rel="noopener">Learn more</a>.</p>
      </div>

      <div class="subscribe-notice__actions">
        <button class="cookie-notice__btn cookie-notice__btn--accept" type="submit">Subscribe</button>
        <button class="cookie-notice__btn cookie-notice__btn--decline" type="button" data-subscribe-dismiss>No thanks</button>
      </div>

      <input type="hidden" name="ht" value="7731c78a1eead3afbdeb40bcadb57061f6b86b07:MTc2OTQ1MDc2NC4xOTk=">
      <input type="hidden" name="mc_signupsource" value="hosted">
    </form>
  </div>

  <div class="cookie-notice notice-card is-hidden" role="dialog" aria-live="polite" aria-label="Cookie notice" data-cookie-notice>
    <p class="cookie-notice__title">Cookies</p>
    <p class="cookie-notice__text">We use cookies to improve your experience.</p>
    <div class="cookie-notice__actions">
      <button class="cookie-notice__btn cookie-notice__btn--accept" type="button" data-cookie-accept>Accept</button>
      <button class="cookie-notice__btn cookie-notice__btn--decline" type="button" data-cookie-decline>Decline</button>
    </div>
  </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
