document.addEventListener('DOMContentLoaded', function(){
  const logo = document.getElementById('wprails-logo');
  const goToHeaven = document.getElementById('wprails-gotoheaven');
  goToHeaven.addEventListener('click', function(){
    logo.classList.add('-heaven');
  })
})
