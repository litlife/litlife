/*
 Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/*
 * Note epub Plugin
 *
 */
(function () {
    CKEDITOR.plugins.add('note',
        {
            init: function (editor) {

                var sectionUrl = editor.config.customValues.sectionUrl;

                editor.addCommand('note', new CKEDITOR.dialogCommand('note', {
                    allowedContent: 'div{*}'
                }));


                editor.ui.addButton('note',
                    {
                        label: 'Добавить сноску',
                        toolbar: 'insert',
                        command: 'note',
                        icon: this.path + 'icons/note.png'
                    });

                CKEDITOR.dialog.add("note", function (instance) {

                    var m = CKEDITOR.plugins.link;

                    return {
                        title: "Добавить сноску",
                        minWidth: 390,
                        minHeight: 230,

                        contents: [{
                            id: "tab1", label: "", title: "Добавить сноску на", expand: !0, padding: 0, elements: [{
                                type: "html",
                                html: '<div id="notePanel" style="padding:10px; overflow-y:scroll; height:500px;">Загружается список глав..</div>'
                            }]
                        }],

                        buttons: [],

                        onShow: function () {

                            var dialog = this;

                            //console.log(dialog);

                            $.getJSON(sectionUrl, function (data) {

                                $("#notePanel").html("");

                                console.log(data);

                                var ul = $('<ul/>');

                                $.each(data, function (i, section) {
                                    var li = $('<li/>');

                                    var span = $('<span/>', {
                                        text: section.title
                                    });

                                    li.append(" ");
                                    li.append(span);

                                    $.each(section.pages, function (i, page) {

                                        $.each(page.html_tags_ids, function (i, id) {

                                            var span = $('<span/>', {
                                                text: "#" + id,
                                                style: "text-decoration:underline; cursor:pointer"
                                            });

                                            span.data('');

                                            span.click(function () {
                                                click(dialog, section.book_id, section.inner_id, page.page, id);
                                            });


                                            li.append(" ");
                                            li.append(span);

                                        });
                                    });

                                    ul.append(li);
                                });

                                $("#notePanel").append(ul);


                            });

                            function click(dialog, book_id, inner_id, page, id) {

                                var selection = editor.getSelection();
                                var selector = selection.getStartElement();
                                var element;

                                var href = "/books/" + book_id + "/sections/" + inner_id + "/";

                                if (page) page = "?page=" + page;
                                if (id) href = href + "#" + id;

                                if (selector) {
                                    element = selector.getAscendant('a', true);
                                }

                                if (!element || element.getName() != 'a') {
                                    element = editor.document.createElement('a');
                                    element.setAttribute("href", href);

                                    if (selection) {
                                        element.setText(selection.getSelectedText());
                                    }
                                    dialog.insertMode = true;
                                }
                                else {
                                    dialog.insertMode = false;
                                }

                                dialog.element = element;

                                //this.setupContent(this.element);

                                //var dialog = this;
                                //var anchorElement = this.element;

                                dialog.commitContent(dialog.element);

                                if (dialog.insertMode) {
                                    editor.insertElement(dialog.element);
                                }

                                dialog.hide();
                            }

                        },
                    }
                });
            }
        });
})();


