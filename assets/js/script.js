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
                <select class="pvg-video-type-dropdown" data-index="` + index + `" name="pvg_video_type[` + index + `]">
                    <option value="youtube">YouTube</option>
                    <option value="facebook">Facebook</option>
                    <option value="wp_library">WP Library</option>
                    <option value="vimeo">Vimeo</option>
                    <option value="rumble">Rumble</option>      
                </select>
                <br>
                <input type="text" id="pvg_video_url_` + index + `" name="pvg_video_url[` + index + `]" placeholder="Paste video URL" style="width: 80%;">
                <span class="pvg_remove_video_icon" style="cursor: pointer; color: red; font-size: 18px;" title="Remove Video">
                    <i class="fas fa-trash-alt"></i>
                </span>
            </div>
        `);
    });

    $('#pvg_video_list').on('change', '.pvg-video-type-dropdown', function () {
        var index = $(this).data('index');
        if ($(this).val() === 'wp_library') {
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
        }
    });

    $('#pvg_video_list').on('click', '.pvg_remove_video_icon', function (e) {
        e.preventDefault();
        $(this).closest('.pvg_video_item').remove();
    });
});
