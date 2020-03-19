let modal = null

const openModal = function (e) {
     e.preventDefault();
     if(modal){closeModal()}
     const target = document.querySelector(e.target.getAttribute('href'));
     target.classList.remove("window-hide");
     target.style.display = null;
     target.classList.add("window-show");
     target.setAttribute('aria-modal', 'true');
     modal = target;
     modal.addEventListener('click', closeModal);
     modal.querySelector('.js-modal-close').addEventListener('click', closeModal);
     modal.querySelector('.js-modal-stop').addEventListener('click', stopPropagation);

}
const closeModal = function (e) {
     if(modal===null) return;
     if(e != null){
          e.preventDefault();
     }
     //e.preventDefault();
     modal.classList.add("window-hide");
     modal.removeAttribute('aria-modal');
     modal.removeEventListener('click', closeModal);
     modal.querySelector('.js-modal-close').removeEventListener('click', closeModal);
     modal.querySelector('.js-modal-stop').removeEventListener('click', stopPropagation);
     if(e != null){
          setTimeout(function(){
               modal.classList.remove("window-show");
               modal.style.display = "none";
               modal = null;
          },500);
     }else{
          modal.classList.remove("window-show");
          modal.style.display = "none";
          modal = null;
     }

}

const stopPropagation = function (e) {
     e.stopPropagation();
}
document.querySelectorAll('.modal-js').forEach(a => {
     a.addEventListener('click', openModal);
})
window.addEventListener('keydown', function (e){
     if(e.key === "Escape" || e.key === "Esc"){
          closeModal(e);
     }
})

$(document).ready(function(){
	$('body').focus(false);
	$('.show-password').click(function() {
		if($(this).prev('input').prop('type') == 'password') {
			//Si c'est un input type password
			$(this).prev('input').prop('type','text');
			$(this).html("<i class='fas fa-eye-slash'></i>");
		} else {
			//Sinon
			$(this).prev('input').prop('type','password');
			$(this).html("<i class='fas fa-eye'></i>");
		}
	});
});
