/*
 Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/*
 * Note epub Plugin
 *
 */
(function () {

    CKEDITOR.plugins.add( 'sectionbreak', {
        icons: 'sectionbreak',
        init: function( editor ) {
            editor.addCommand( 'sectionbreak', {
                exec: function( editor ) {

                    //editor.editable().insertHtmlIntoRange( '<div class="u-section-break" />' );
                    var parents = editor.getSelection().getStartElement().getParents();

                    var elementP = parents[2];

                    var range = editor.createRange(),
                        element = CKEDITOR.dom.element.createFromHtml( '<hr class="u-section-break" />' );

                    // Place range before the <p> element.
                    range.setStartAt( elementP, CKEDITOR.POSITION_AFTER_END );

                    // Make sure it's collapsed.
                    range.collapse( true );

                    // Insert element at the range position.
                    editor.editable().insertElement( element, range );
                }
            });
            editor.ui.addButton( 'sectionbreak', {
                label: 'Вставить разрез на главы',
                toolbar: 'insert',
                command: 'sectionbreak',
                icon: this.path + 'icons/sectionbreak.png'
            });
        }
    });

})();


