;(function ($, document, window) {
    'use strict';

    /**
     * Batch View plugin.
     */
    function ViewBatch(element, options)
    {
        this.element = element;
        this.options = options;
        this.helper = {
            'msg': '<span class="help-block" {{id}} style="cursor:pointer;"><i class="fa fa-{{icon}}"></i> {{action}}</span>',
            'enable_id': 'data-batch="enable"',
            'disable_id': 'data-batch="disable"'
        };

        this.updateForm();
        this.init();
    }

    ViewBatch.prototype = {

        /**
         * Initialize method
         *
         * @return {undefined}
         */
        init: function () {
            var that = this;

            $(that.options.target_id).each(function () {
                $(this).attr('disabled', true);
                var msg = that.helper.msg
                    .replace('{{id}}', that.helper.enable_id)
                    .replace('{{icon}}', 'check-circle')
                    .replace('{{action}}', 'Click to edit');
                $(this).parents(that.options.wrapper_id).append(msg);
            });

            $(document).on('click', that.options.disable_id, function () {
                var field = $(this).parent().find(that.options.target_id);
                var msg = that.helper.msg
                    .replace('{{id}}', that.helper.enable_id)
                    .replace('{{icon}}', 'check-circle')
                    .replace('{{action}}', 'Click to edit');
                $(field).parents(that.options.wrapper_id).append(msg);
                $('[name="' + $(field).attr('name') + '"]').attr('disabled', true);
                $(this).remove();
            });

            $(document).on('click', that.options.enable_id, function () {
                var field = $(this).parent().find(that.options.target_id);
                var msg = that.helper.msg
                    .replace('{{id}}', that.helper.disable_id)
                    .replace('{{icon}}', 'times-circle')
                    .replace('{{action}}', 'Do not change');
                $(field).parents(that.options.wrapper_id).append(msg);
                $('[name="' + $(field).attr('name') + '"]').attr('disabled', false);
                $(field).focus();
                $(this).remove();
            });
        },

        updateForm: function () {
            var that = this;

            // update form action
            $(this.element).attr('action', $(this.element).attr('action') + '/edit');

            // add batch ids to the form as hidden inputs
            $(this.options.batch_ids).each(function () {
                $(that.element).append('<input type="hidden" name="batch[ids][]" value="' + this + '">');
            });

            // add batch execute flag
            $(that.element).append('<input type="hidden" name="batch[execute]" value="1">');

            // add referer url, for redirect purposes
            $(that.element).append(
                '<input type="hidden" name="batch[redirect_url]" value="' + this.options.redirect_url + '">'
            );
        }
    };

    $.fn.viewBatch = function (options) {
        return this.each(function () {
            new ViewBatch(this, options);
        });
    };

})(jQuery, document, window);
