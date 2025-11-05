/**
 * GUploads
 * Javascript multiples upload
 *
 * @filesource js/uploads.js
 * @link https://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 */
(function() {
  "use strict";
  window.GUploads = GClass.create();
  GUploads.prototype = {
    initialize: function(options) {
      this.options = {
        form: "",
        input: "",
        fileprogress: "",
        fileext: ["jpg", "jpeg", "webp", "gif", "png"],
        onupload: $K.emptyFunction,
        oncomplete: $K.emptyFunction,
        customSettings: {}
      };
      Object.extend(this.options, options || {});
      this.form = $G(this.options.form);
      this.frmContainer = document.createElement("div");
      this.frmContainer.style.display = "none";
      this.form.appendChild(this.frmContainer);
      this.index = 0;
      this.count = 0;
      var input = $G(this.options.input);
      this.prefix = input.get("id");
      this.parent = $G(input.parentNode);
      this.size = input.get("size");
      this.className = input.className;
      this.name = input.get("name");
      this.multiple = window.FormData ? true : false;
      input.multiple = this.multiple;
      this.result = $E(this.options.fileprogress);
      var temp = this;
      var _doUploadChanged = function(e) {
        var index = 0;
        var doProgress = function(val) {
          $E("bar_" + temp.prefix + "_" + index).style.width = val + "%";
        };
        var xhr = new GAjax({
          onProgress: doProgress,
          contentType: null
        });

        function _upload(files, i) {
          var total = 0;
          if (temp.uploading && i < files.length) {
            index = i;
            if ($E("p_" + temp.prefix + "_" + index)) {
              var f = files[i];
              var data = new FormData();
              for (var name in temp.options.customSettings) {
                data.append(
                  name,
                  encodeURIComponent(temp.options.customSettings[name])
                );
              }
              data.append("file", f);
              $G("close_" + temp.prefix + "_" + index).remove();
              xhr.send(temp.form.action, data, function(xhr) {
                var ds = xhr.responseText.toJSON();
                if (ds) {
                  if (ds.alert) {
                    $G("result_" + temp.prefix + "_" + index).addClass(
                      "invalid"
                    ).innerHTML =
                      ds.alert;
                    temp.error.push(ds.alert);
                  }
                } else if (xhr.responseText != "") {
                  $G("result_" + temp.prefix + "_" + index).addClass(
                    "invalid"
                  ).innerHTML =
                    xhr.responseText;
                  temp.error.push(xhr.responseText);
                } else {
                  $G("p_" + temp.prefix + "_" + index).remove();
                  total++;
                }
                _upload(files, index + 1);
              });
            } else {
              _upload(files, index + 1);
            }
          } else {
            temp.options.oncomplete.call(temp, temp.error.join("\n"), total);
          }
        }
        if (temp.multiple) {
          forEach(this.files, function() {
            var file = temp._ext(this.name);
            if (temp.options.fileext.indexOf(file.ext) != -1) {
              temp._display(file);
              temp.count++;
              temp.index++;
            }
          });
          temp.uploading = true;
          temp.options.onupload.call(temp);
          _upload(this.files, 0);
        } else {
          var file = temp._ext(this.value);
          if (temp.options.fileext.indexOf(file.ext) != -1) {
            temp._display(file);
            var form = document.createElement("form");
            form.id = "form_" + temp.prefix + "_" + temp.index;
            form.action = temp.form.action;
            temp.frmContainer.appendChild(form);
            this.removeEvent("change", _doUploadChanged);
            form.appendChild(this);
            for (var name in temp.options.customSettings) {
              $G(form).create("input", {
                name: name,
                value: encodeURIComponent(temp.options.customSettings[name]),
                type: "hidden"
              });
            }
            temp.count++;
            temp.index++;
            var _input = $G(temp.parent).create("input", {
              id: temp.prefix + temp.index,
              name: temp.name,
              class: temp.className,
              type: "file"
            });
            _input.addEvent("change", _doUploadChanged);
          } else {
            alert(trans("The type of file is invalid"));
          }
        }
      };
      input.addEvent("change", _doUploadChanged);
      this.uploading = false;
      this.error = new Array();
      var _submit = function(forms, index) {
        var total = 0;
        var id = forms[index].id;
        var result = $E(id.replace("form_", "result_"));
        $G(id.replace("form_", "close_")).remove();
        result.className = "icon-loading";
        var frm = new GForm(id);
        frm.result = result;
        frm.submit(function(xhr) {
          var ds = xhr.responseText.toJSON();
          if (ds) {
            if (ds.alert) {
              frm.result.innerHTML = ds.alert;
              frm.result.className = "icon-invalid";
              temp.error.push(ds.alert);
            }
          } else if (xhr.responseText != "") {
            frm.result.innerHTML = xhr.responseText;
            frm.result.className = "icon-invalid";
            temp.error.push(xhr.responseText);
          } else {
            frm.result.className = "icon-valid";
            total++;
          }
          index++;
          if (index < forms.length && temp.uploading) {
            _submit.call(temp, forms, index);
          } else {
            temp.options.oncomplete.call(temp, temp.error.join("\n"), total);
          }
        });
      };
      this.form.addEvent("submit", function(e) {
        GEvent.stop(e);
        if (!temp.uploading && temp.index > 0) {
          temp.uploading = true;
          $E(temp.prefix + temp.index).disabled = "disabled";
          temp.options.onupload.call(temp);
          total = 0;
          _submit(temp.frmContainer.getElementsByTagName("form"), 0);
        }
      });
    },
    cancel: function() {
      this.uploading = false;
    },
    _ext: function(name) {
      var obj = new Object();
      var files = name.replace(/\\/g, "/").split("/");
      obj.name = files[files.length - 1];
      var exts = obj.name.split(".");
      obj.ext = exts[exts.length - 1].toLowerCase();
      return obj;
    },
    _display: function(file) {
      var p = document.createElement("p");
      p.className = "upload-entry";
      this.result.appendChild(p);
      p.id = "p_" + this.prefix + "_" + this.index;
      var icon = this._createIcon(file.ext);
      p.appendChild(icon);
      var body = document.createElement("div");
      body.className = "upload-entry__body";
      p.appendChild(body);
      var title = document.createElement("span");
      title.className = "upload-entry__name";
      title.textContent = file.name;
      body.appendChild(title);
      var a = document.createElement("a");
      a.className = "icon-delete upload-entry__remove";
      a.id = "close_" + this.prefix + "_" + this.index;
      a.href = "#";
      p.appendChild(a);
      var temp = this;
      callClick(a, function() {
        temp._remove(this.id.replace("close_" + temp.prefix + "_", ""));
      });
      var status = document.createElement("span");
      status.className = "upload-entry__status";
      body.appendChild(status);
      status.id = "result_" + this.prefix + "_" + this.index;
      if (this.multiple) {
        var bar = document.createElement("span");
        bar.className = "bar_graphs";
        body.appendChild(bar);
        var span = document.createElement("span");
        span.className = "value_graphs";
        span.id = "bar_" + this.prefix + "_" + this.index;
        bar.appendChild(span);
      }
    },
    _createIcon: function(ext) {
      var config = this._iconConfig(ext);
      var span = document.createElement("span");
      span.className = "file-icon";
      span.setAttribute("data-tone", config.tone);
      span.textContent = config.label;
      span.title = config.title;
      return span;
    },
    _iconConfig: function(ext) {
      var key = (ext || "").toLowerCase();
      var labels = {
        aiff: {label: "AIFF", tone: "secondary"},
        avi: {label: "AVI", tone: "secondary"},
        bmp: {label: "BMP", tone: "secondary"},
        c: {label: "C", tone: "secondary"},
        cpp: {label: "CPP", tone: "secondary"},
        css: {label: "CSS", tone: "brand"},
        dll: {label: "DLL", tone: "muted"},
        doc: {label: "DOC", tone: "brand"},
        docx: {label: "DOCX", tone: "brand"},
        exe: {label: "EXE", tone: "muted"},
        flv: {label: "FLV", tone: "secondary"},
        gif: {label: "GIF", tone: "accent"},
        htm: {label: "HTML", tone: "brand"},
        html: {label: "HTML", tone: "brand"},
        iso: {label: "ISO", tone: "muted"},
        jpeg: {label: "JPG", tone: "accent"},
        jpg: {label: "JPG", tone: "accent"},
        js: {label: "JS", tone: "brand"},
        midi: {label: "MIDI", tone: "secondary"},
        mov: {label: "MOV", tone: "secondary"},
        mp3: {label: "MP3", tone: "secondary"},
        mpg: {label: "MPG", tone: "secondary"},
        ogg: {label: "OGG", tone: "secondary"},
        pdf: {label: "PDF", tone: "danger"},
        php: {label: "PHP", tone: "brand"},
        png: {label: "PNG", tone: "accent"},
        ppt: {label: "PPT", tone: "warm"},
        pptx: {label: "PPTX", tone: "warm"},
        psd: {label: "PSD", tone: "secondary"},
        rar: {label: "RAR", tone: "muted"},
        rm: {label: "RM", tone: "muted"},
        rtf: {label: "RTF", tone: "brand"},
        sql: {label: "SQL", tone: "brand"},
        swf: {label: "SWF", tone: "muted"},
        tar: {label: "TAR", tone: "muted"},
        tgz: {label: "TGZ", tone: "muted"},
        tiff: {label: "TIFF", tone: "accent"},
        txt: {label: "TXT", tone: "secondary"},
        wav: {label: "WAV", tone: "secondary"},
        wma: {label: "WMA", tone: "secondary"},
        wmv: {label: "WMV", tone: "secondary"},
        xls: {label: "XLS", tone: "success"},
        xml: {label: "XML", tone: "brand"},
        xvid: {label: "XVID", tone: "secondary"},
        zip: {label: "ZIP", tone: "muted"},
        webp: {label: "WEBP", tone: "accent"}
      };
      var config = labels[key] || {
        label: (key || "file").substring(0, 4).toUpperCase(),
        tone: "neutral"
      };
      return {
        label: config.label,
        tone: config.tone,
        title: key ? key.toUpperCase() + " file" : "File"
      };
    },
    _remove: function(index) {
      $G("p_" + this.prefix + "_" + index).remove();
      $G("form_" + this.prefix + "_" + index).remove();
      this.count--;
    }
  };
})();

function initGUploads(form_id, id, model, module_id) {
  var patt = /^(delete)_([0-9]+)(_([0-9]+))?$/,
    form = $G(form_id);
  if (G_Lightbox === null) {
    G_Lightbox = new GLightbox();
  } else {
    G_Lightbox.clear();
  }
  var _doDelete = function() {
    var cs = new Array();
    forEach(form.elems("a"), function() {
      if (this.className == "icon-check") {
        var hs = patt.exec(this.id);
        cs.push(hs[2]);
      }
    });
    if (cs.length == 0) {
      alert(trans("Please select at least one item").replace(/XXX/, this.innerHTML));
    } else if (confirm(trans("You want to XXX the selected items ?").replace(/XXX/, this.innerHTML))) {
      _action("action=deletep&mid=" + module_id + "&aid=" + id + "&id=" + cs.join(","));
    }
  };
  var _doAction = function(e) {
    var hs = patt.exec(this.id);
    if (hs[1] == "delete") {
      this.className = this.className == "icon-check" ? "icon-uncheck" : "icon-check";
    }
    GEvent.stop(e);
    return false;
  };

  function _action(q) {
    send("index.php/" + model, q, doFormSubmit);
  }

  forEach(form.elems("a"), function() {
    var hs = patt.exec(this.id);
    if (hs) {
      callClick(this, _doAction);
      const backgroundImage = this.parentNode.style.backgroundImage
        .replace('url(', '')
        .replace(/['"\(\)]+/g, '')
        .replace('thumb_', '');
      let id = G_Lightbox.add(backgroundImage);
      callClick(this.parentNode, function() {
        G_Lightbox.play(id);
      });
    }
  });

  new GDragDrop(form_id, {
    endDrag: function() {
      var elems = new Array();
      forEach($G(form_id).elems("figure"), function() {
        if (this.id) {
          elems.push(this.id.replace("L_", ""));
        }
      });
      if (elems.length > 1) {
        _action("action=sort&mid=" + module_id + "&aid=" + id + "&id=" + elems.join(","));
      }
    }
  });

  var _setSel = function() {
    var chk = this.id == "selectAll" ? "icon-check" : "icon-uncheck";
    forEach(form.elems("a"), function() {
      var hs = patt.exec(this.id);
      if (hs && hs[1] == "delete") {
        this.className = chk;
      }
    });
  };

  var galleryUploadResult = function(error, count) {
    if (error != "") {
      alert(error);
    }
    if (count > 0) {
      alert(trans("Successfully uploaded XXX files").replace("XXX", count));
    }
    if (loader) {
      loader.reload();
    } else {
      window.location.reload();
    }
  };

  var upload = new GUploads({
    form: form_id,
    input: "fileupload_tmp",
    fileprogress: "fsUploadProgress",
    oncomplete: galleryUploadResult,
    onupload: function() {
      $E("btnCancel").disabled = false;
    },
    customSettings: {albumId: id}
  });
  callClick("btnCancel", function() {
    upload.cancel();
  });
  callClick("btnDelete", _doDelete);
  callClick("selectAll", _setSel);
  callClick("clearSelected", _setSel);
}
