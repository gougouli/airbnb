$(document).ready(function() {
     /* Every time the window is scrolled ... */

     $(window).scroll( function(){
          if(window.innerWidth > 1200){
               var pas = $(window).height()/100;
               var valeur = $(window).scrollTop()/pas/50;
               if (valeur < 0.9) {
                    var opa = valeur;
               }
               $('nav').css('background-color', 'rgba(0, 59, 92 , '+ opa +')');

          }


          /* Check the location of each desired element */
          $('.appear').each(function(i){
               /*
               -> $(this).offset().top -> La hauteur du bas d'un bloc
               -> $(this).height() -> Hauteur d'un bloc
               -> $(window).scrollTop() -> Hauteur du haut de la fenetre
               */

               var bottom_of_object = $(this).offset().top + ($(this).height()/2);
               var bottom_of_window = $(window).scrollTop() + $(window).height();
               /* If the object is completely visible in the window, fade it it */
               if( bottom_of_window > bottom_of_object  ){
                    $(this).animate({'opacity':'1'},600);

               }
          });
     });
});
