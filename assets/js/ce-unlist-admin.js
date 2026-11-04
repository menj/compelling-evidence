/**
 * Quick Edit: tick "Unlist this post" from the row's Unlisted column
 * (inc/ce-unlist.php).
 */
(function ($) {
  'use strict';
  if (typeof inlineEditPost === 'undefined') return;
  var origEdit = inlineEditPost.edit;
  inlineEditPost.edit = function (id) {
    origEdit.apply(this, arguments);
    var postId = parseInt(typeof id === 'object' ? this.getId(id) : id, 10);
    if (!postId) return;
    var unlisted = $('#post-' + postId).find('.ce-unlist-status').data('unlisted');
    $('.inline-edit-row').find('input[name="ce_unlisted_qe"]').prop('checked', String(unlisted) === '1');
  };
})(jQuery);
