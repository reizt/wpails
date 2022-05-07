/**
 * WPAILSで使う二重送信防止機能
 */
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
