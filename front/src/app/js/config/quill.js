import Quill from 'quill/core';
import Emitter from 'quill/core/emitter';

import Toolbar from 'quill/modules/toolbar';
import Snow from 'quill/themes/snow';

import Bold from 'quill/formats/bold';
import Italic from 'quill/formats/italic';
import Header from 'quill/formats/header';

import Delta from 'quill-delta';

import {API_PREFIX} from 'upont/js/config/constants';

export const uploadToAPIImageHandler = function() {
    let fileInput = this.container.querySelector('input.ql-image[type=file]');
    if (fileInput == null) {
        fileInput = document.createElement('input');
        fileInput.setAttribute('type', 'file');
        fileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/bmp, image/x-icon');
        fileInput.classList.add('ql-image');
        fileInput.addEventListener('change', () => {
            if (fileInput.files != null && fileInput.files[0] != null) {
                const formData = new FormData();
                formData.append('file', fileInput.files[0]);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', API_PREFIX + 'images?bearer=' + localStorage.getItem('token'), true);
                xhr.onload = () => {
                    if (xhr.status === 201) {
                        const url = JSON.parse(xhr.responseText).filelink;
                        let range = this.quill.getSelection(true);
                        this.quill.updateContents(
                            new Delta().retain(range.index).delete(range.length).insert({image: url}),
                            Emitter.sources.USER
                        );
                        fileInput.value = "";
                    }
                };
                xhr.send(formData);
            }
        });
        this.container.appendChild(fileInput);
    }
    fileInput.click();
};

Quill.register({'modules/toolbar': Toolbar, 'themes/snow': Snow});

export default Quill;
