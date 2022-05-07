function getMeta(metaName) {
  let metas = document.getElementsByTagName('meta');
  for (let i = 0; i < metas.length; i++) {
    if (metas[i].getAttribute('name') === metaName) {
      return metas[i].getAttribute('content');
    }
  }
}
// CSRF対策
function insertCsrfToken($form){
  $form.append(`
    <input type="hidden" name="csrf-token" value="${getMeta('csrf-token')}">
  `);
}
$(document).on('submit', 'form[method=post]', function(event){
  event.preventDefault();
  const $form = $(this);
  insertCsrfToken($form);
  $form.find('input[type=submit]').attr('disabled', true);// 二重送信防止
  $form[0].submit();
});
$(document).on('input', '.SubmitNow', function(){
  const $form = $(this).parents('form');
  if($form[0].method === 'post'){
    insertCsrfToken($form);
  }
  $form[0].submit();
});
$(document).on('click', '.DeleteSubmitter', function(){
  const $form = $(this).parents('form');
  insertCsrfToken($form);
  $form.append(`
    <input type="hidden" name="delete_flag" value="on">
  `);
  $form[0].submit();
});
$(document).on('click', '.ModalOpener', function(){
  const FADEIN_MS = 200;
  const modalID = $(this).data('target');
  $('#' + modalID).fadeIn(FADEIN_MS);
  $('#controlpanel-modal-mask').show();
  document.body.style.overflow = 'hidden';
});
$(document).on('click', '.ModalCloser', function(){
  const FADEOUT_MS = 200;
  $('.controlpanel-modal').fadeOut(FADEOUT_MS);
  $('#controlpanel-modal-mask').hide();
  document.body.style.overflow = 'auto';
});
