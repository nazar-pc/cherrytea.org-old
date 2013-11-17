// Generated by CoffeeScript 1.4.0

/**
 * @package		Plupload
 * @category	modules
 * @author		Moxiecode Systems AB
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com> (integration into CleverStyle CMS)
 * @copyright	Moxiecode Systems AB
 * @license		GNU GPL v2, see license.txt
*/


/**
 * Files uploading interface
 *
 * @param {object}		button
 * @param {function}	success
 * @param {function}	error
 * @param {function}	progress
 * @param {bool}		multi
 *
 * @return {function}
*/


(function() {

  cs.file_upload = function(button, success, error, progress, multi) {
    var browse_button, files, uploader, _ref, _ref1;
    files = [];
    browse_button = $('<button id="plupload_' + (new Date).getTime() + '" style="display:none;"/>').appendTo('body');
    uploader = new plupload.Uploader({
      browse_button: browse_button.get(0),
      max_file_size: (_ref = (_ref1 = cs.plupload) != null ? _ref1.max_file_size : void 0) != null ? _ref : null,
      multi_selection: multi,
      multipart: true,
      runtimes: 'html5',
      url: '/Plupload'
    });
    uploader.init();
    if (button) {
      button.click(function() {
        return setTimeout((function() {
          var input;
          input = browse_button.nextAll('.moxie-shim:first').children();
          if (!input.attr('accept')) {
            input.removeAttr('accept');
          }
          return browse_button.click();
        }), 0);
      });
    }
    uploader.bind('FilesAdded', function() {
      uploader.refresh();
      return uploader.start();
    });
    if (progress) {
      uploader.bind('UploadProgress', function(uploader, file) {
        return progress(file.percent, file.size, file.loaded, file.name);
      });
    }
    if (success) {
      uploader.bind('FileUploaded', function(uploader, files_, res) {
        var response;
        response = $.parseJSON(res.response);
        if (!response.error) {
          return files.push(response.result);
        } else {
          return alert(response.error.message);
        }
      });
      uploader.bind('UploadComplete', function() {
        success(files);
        return files = [];
      });
    }
    if (error) {
      uploader.bind('Error', function(uploader, error) {
        return error(error);
      });
    }
    this.stop = function() {
      return uploader.stop();
    };
    this.destroy = function() {
      browse_button.remove();
      uploader.destroy();
      return $('.moxie-shim').each(function() {
        if ($(this).html() === '') {
          return $(this).remove();
        }
      });
    };
    this.browse = function() {
      return setTimeout((function() {
        var input;
        input = browse_button.nextAll('.moxie-shim:first').children();
        if (!input.attr('accept')) {
          input.removeAttr('accept');
        }
        return browse_button.click();
      }), 0);
    };
    return this;
  };

  return;

}).call(this);
