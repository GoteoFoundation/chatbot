{{-- VIEW INTENDED FOR DEVELOPMENT ONLY --}}
{{-- Accessible just in development environment by admin user --}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>

    {{-- Example widget HTML starts --}}

    {{-- <div id="goteo-help-widget" class="icon" style="display: none;">

      <a class="widget-icon" tabindex="0" title="We help you">
        <span>We help you</span>
      </a>

      <div class="widget-container" role="dialog" aria-labelledby="goteo-help-widget-title">
        <div class="widget-header">
          <h5 id="goteo-help-widget-title">Help</h5>
          <a class="widget-back" tabindex="0" title="Back" aria-hidden="true"><span>Back</span></a>
          <a class="widget-restart" tabindex="0" title="Restart" aria-hidden="true"><span>Restart</span></a>
          <a class="widget-close" tabindex="0" title="Close"><span>Close</span></a>
        </div>

        <div class="widget-questions" aria-hidden="true">

          <div class="widget-question" data-id="1">
            <h6>Example of question?</h6>
            <ul class="widget-answers">
              <li><a tabindex="0" data-question-link="2">Example of answer 1</a></li>
              <li><a tabindex="0" data-question-link="3">Example of answer 3</a></li>
              <li><a href="#" target="_blank">Example of linked answer 3</a></li>
              <li><a tabindex="0" data-question-link="4">Example of answer 4</a></li>
            </ul>
          </div>

        </div>

        <div class="widget-welcome">
          <p>Welcome ipsum dolor sit amet, consectetur adipiscing elit.</p>
          <button class="widget-start">Start</button>
        </div>

        <div class="widget-error" aria-hidden="true">
          <div class="widget-error-content">
            <h6>Uops!</h6>
            <p>We could not retrieve help information</p>
            <button class="widget-retry">Retry</button>
          </div>
        </div>

        <div class="widget-loading" aria-hidden="true">
          <p>Loading...</p>
        </div>

        <div class="widget-footer">
          <a class="widget-faq" href="#" target="_blank" title="Frequently Asked Questions"><span>Frequently Asked Questions</span></a>
        </div>

      </div>

    </div> --}}

    {{-- Example widget HTML ends --}}

    <!-- Styles, already loaded by the script -->
    {{-- <link href="{{ asset('widget/widget.css') }}" rel="stylesheet"> --}}

    <!-- Dynamic Widget Script -->
    <script src="{{ asset('widget/widget-jquery.js') }}"></script>
    <script>
      (window.goteoHelpWidget=window.goteoHelpWidget||{}).load("http://goteo.local", "es", 1, true);
    </script>

  </body>
</html>
