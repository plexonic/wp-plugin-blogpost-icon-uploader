/**
 * Created with JetBrains PhpStorm.
 * User: Grigor Galastyan
 * Date: 7/9/13
 * Time: 4:14 PM
 * To change this template use File | Settings | File Templates.
 */

var deleteButton = document.querySelector("#delete-post-image");
var undoButton = document.querySelector("#post-image-undo-link");


deleteButton.addEventListener("click", function( e ) {
    document.querySelector("#post-image-preview").className = "hidden";
    document.querySelector("#post-image-undo").className = "";

    document.querySelector("#delete-image-flag").value = "on";
    e.preventDefault();
}, false);

undoButton.addEventListener("click", function( e ) {
    document.querySelector("#post-image-preview").className = "";
    document.querySelector("#post-image-undo").className = "hidden";

    document.querySelector("#delete-image-flag").value = "";
    e.preventDefault();
}, false);




var deleteButton_new = document.querySelector("#delete-post-image-new");
var undoButton_new = document.querySelector("#post-image-undo-link-new");

deleteButton_new.addEventListener("click", function( e ) {
//    document.querySelector("#post-image-preview-new").className = "hidden";
//    document.querySelector("#post-image-undo-new").className = "";
//
//    document.querySelector("#delete-image-flag").value = "on";
//    e.preventDefault();
    alert('ssxcscxs');
});
