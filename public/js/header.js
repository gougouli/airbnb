$(document).ready(function() {
     $(".menu-burger").click(function() {
          event.preventDefault();
          $("nav").css('height','auto');
          $("nav").css('justify-content', 'center');
          $("nav").css('flex-direction', 'column');
          $(".menu-burger").css('display', 'none');
          $("h1").css('display', 'none');
          $("nav ul").css('display', 'flex');
          $(".menu-cross").css('display', 'block');
     });
     $(".menu-cross").click(function() {
          event.preventDefault();
          $("nav").css('flex-direction', 'row');
          $("nav").css('justify-content', 'space-between');
          $(".menu-burger").css('display', 'flex');
          $("h1").css('display', 'block');
          $("nav ul").css('display', 'none');
          $(".menu-cross").css('display', 'none');
     });
});
