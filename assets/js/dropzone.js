// If you are using JavaScript/ECMAScript modules:
import Dropzone from "dropzone";

// If you are using CommonJS modules:
// const { Dropzone } = require("dropzone");

// If you are using an older version than Dropzone 6.0.0,
// then you need to disabled the autoDiscover behaviour here:


window.onload = (event) => {
    Dropzone.autoDiscover = false;
// console.log(document.getElementById("#js-documents"))
    let nameform = document.getElementsByName('document_form');
    // console.log(nameform[0]);
    // debugger

    let myDropzone = new Dropzone("#my-form",{
        url: nameform[0].dataset.url, 
        paramName: 'reference',
        init: function() {
            this.on('success', function(file, data) {
                referenceList.addReference(data);
            });

            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });
        }
    });
    myDropzone.on("addedfile", file => {
      console.log(`File added: ${file.name}`);
    });
  };
