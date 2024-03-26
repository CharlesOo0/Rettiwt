$(document).ready(function() {
    $(".like-form").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: "ajax_request/handleLike.php",
            data: form.serialize(),
            success: function(data) {
                if (data == 1) {
                    var likeCount = form.find(".like-count");
                    var newCount = parseInt(likeCount.text()) + 1; // Parse the text to an integer and add 1
                    likeCount.text(newCount); // Set the text of the element to the new count
                    form.find(".like-button").attr("src", "img/like_filled.png")
                } else {
                    var likeCount = form.find(".like-count");
                    var newCount = parseInt(likeCount.text()) - 1; // Parse the text to an integer and subtract 1
                    likeCount.text(newCount); // Set the text of the element to the new count
                    form.find(".like-button").attr("src", "img/like_empty.png")
                }
            }
        });
    });
});