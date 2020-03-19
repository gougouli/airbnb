let children = $('.block-tendance > *').length - 2;
let number = 0;
let width = $(".accomodation").outerWidth(true);
let divPerPage = Math.round($(window).outerWidth(true) / width)-1;
let numberMax = Math.abs((children - divPerPage)) / 2;
let leftMax = -numberMax * width ;
let rightMax = -leftMax;
const goSliderRight = function (e) {
     e.preventDefault();
     if(number > -numberMax){
          number -=1;
          $(".function-left").show();
          $(".block-tendance").children(".accomodation").css("transform","translate(" + number*width + "px,0)");

     }else{
          number = number *-1;
          $(".block-tendance").children(".accomodation").css("transform","translate(" + rightMax + "px,0)");
     }
}

const goSliderLeft = function (e) {
     e.preventDefault();
     if(number < numberMax){
          number +=1;
          $(".function-right").show();
          $(".block-tendance").children(".accomodation").css("transform","translate(" + number*width + "px,0)");
     }else{
          number = number *-1;
          $(".block-tendance").children(".accomodation").css("transform","translate(" + leftMax + "px,0)");
     }
}

document.querySelectorAll('.function-right').forEach(a => {
     a.addEventListener('click', goSliderRight)
})
document.querySelectorAll('.function-left').forEach(a => {
     a.addEventListener('click', goSliderLeft)
})

$(document).ready(function() {
     if((children <= 5 && window.innerWidth > 1200) || (children < 2 && window.innerWidth < 1200 )){
          $('main .block-tendance .btn_right').css('display','none');
          $('main .block-tendance .btn_left').css('display','none');
     }else{
          $('main .block-tendance .btn_right').css('display','flex');
          $('main .block-tendance .btn_left').css('display','flex');
     }
});
