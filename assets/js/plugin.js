/**
 * Created with JetBrains PhpStorm.
 * User: Grigor Galastyan
 * Date: 7/9/13
 * Time: 4:14 PM
 * To change this template use File | Settings | File Templates.
 */

var deleteButton = document.querySelector("#delete-post-image");

if ( deleteButton ) {
    deleteButton.addEventListener("click", function( e ) {
        document.querySelector("#post-image-preview").className = "hidden";
        document.querySelector("#delete-image-flag").value = "on";
        e.preventDefault();
    }, false);
}

