$(function(){

    $('.select2-languages').select2({
      placeholder: goteoData.translations.languageSelectPlaceholder,
      ajax: {
        url: goteoData.languageIndexEndpoint  + '/' + (Object.is(goteoData.currentLanguageId, undefined) ? '0' : goteoData.currentLanguageId)
      }
    });

    if(goteoData.selectedOption) {
      var languagesSelect = $('select.select2-languages');

      $.ajax({
        type: 'GET',
        url: goteoData.languageIndexEndpoint + '/' + (Object.is(goteoData.currentLanguageId, undefined) ? '0' : goteoData.currentLanguageId) + '/' + goteoData.selectedOption,
        success: function(data) {
          var option = new Option(data.results[0].text, data.results[0].id, true, true);

          languagesSelect.append(option).trigger('change');

          // Manually trigger the 'select2:select' event
          languagesSelect.trigger({
            type: 'select2:select',
            params: {
              data: data
            }
          });
        }
      });
    }
});
