var alert = document.querySelector('.alert');

if(alert != undefined){
    var closeBtn = document.createElement('button');
        closeBtn.appendChild(document.createTextNode('X'));
        closeBtn.setAttribute('title','Close Alert');
        closeBtn.classList.add('alert-close-btn');
        closeBtn.onclick = function(){
          this.parentElement.remove();
        };
      alert.appendChild(closeBtn);
}
