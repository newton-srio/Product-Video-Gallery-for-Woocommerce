jQuery(document).ready(function ($) {
    if ($('.pvg-video-slider').length && typeof $.fn.slick !== 'undefined') {
        $('.pvg-video-slider').slick({
            dots: true,
            arrows: true,
            infinite: false,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true,
            prevArrow: '<button type="button" class="slick-prev slick-arrow-custom"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next slick-arrow-custom"><i class="fas fa-chevron-right"></i></button>'
        });
    }

    $('#pvg_add_more_videos').click(function (e) {
        e.preventDefault();
        var index = $('#pvg_video_list .pvg_video_item').length;

        $('#pvg_video_list').append(`
            <div class="pvg_video_item">
                <label>Choose Video Type:</label><br>
                <input type="radio" class="pvg-video-type-radio" data-index="` + index + `" name="pvg_video_type[` + index + `]" value="youtube"> YouTube
                <input type="radio" class="pvg-video-type-radio" data-index="` + index + `" name="pvg_video_type[` + index + `]" value="vimeo"> Vimeo
                <input type="radio" class="pvg-video-type-radio" data-index="` + index + `" name="pvg_video_type[` + index + `]" value="wp_library"> WP Library
                <br>
                <input type="text" id="pvg_video_url_` + index + `" name="pvg_video_url[` + index + `]" placeholder="Paste video URL or upload" style="width: 80%;">
                <button class="button pvg_upload_button" data-index="` + index + `" style="display:none;">Upload from WP Library</button>
                <button class="button pvg_remove_video">Remove Video</button>
            </div>
        `);
    });

    $('#pvg_video_list').on('change', '.pvg-video-type-radio', function () {
        var index = $(this).data('index');
        if ($(this).val() === 'wp_library') {
            $('#pvg_video_url_' + index).siblings('.pvg_upload_button').show();
        } else {
            $('#pvg_video_url_' + index).siblings('.pvg_upload_button').hide();
        }
    });
    $('#pvg_video_list').on('click', '.pvg_remove_video', function (e) {
        e.preventDefault();
        $(this).closest('.pvg_video_item').remove();
    });

    $('#pvg_video_list').on('click', '.pvg_upload_button', function (e) {
        e.preventDefault();

        var index = $(this).data('index');
        var mediaUploader = wp.media({
            title: 'Select or Upload Video',
            button: {
                text: 'Use this video'
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#pvg_video_url_' + index).val(attachment.url);
        });

        mediaUploader.open();
    });
});
