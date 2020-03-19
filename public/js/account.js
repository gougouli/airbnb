let name= ["fullname", "email", "phone", "idcard", "bio"];
let i = 0;
$(function(){
     $(".form-custom-info").submit(function(event){
          if($('button.custom').children('span').html() == " Modifier vos informations"){
               event.preventDefault();
               if(i == 0){
                    $('.edit').each(function(){
                         let value = $(this).children().html();
                         $(this).children().remove();
                         if(value.length < 100){
                              $(this).append('<input type=text name="'+name[i]+'" value="'+value+'">');
                         }else{
                              $(this).append('<textarea name="'+name[i]+'">'+value+'</textarea>');
                         }
                         i++;
                    });
                    setTimeout(function(){$('button.custom').children('span').html(" Valider vos informations");i = 0;},1000);
               }
          }
     });
});
function chooseFile() {
   $("#fileInput").click();
}
