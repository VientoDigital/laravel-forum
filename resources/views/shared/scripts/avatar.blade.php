<script type="text/javascript">
    function makeAvatar () {
        var el = document.querySelectorAll('[avatar]');
        for (var i in el) {

            if(typeof el[i] === 'object' && typeof el[i].getAttribute('avatar') === 'string') {
                el[i].classList.add('bg-primary','text-light','d-inline-block','text-center','rounded-circle');
                el[i].style.height= '40px';
                el[i].style.width='40px';
                el[i].style.lineHeight='40px';
                
                var letters = '';
                var sec = el[i].getAttribute('avatar').trim().split(' ');
                for (var current of sec) {
                    if (current.length > 0) {
                        letters += current[0];
                        if (letters.length > 1) {
                            break;
                        }
                    }
                }
                el[i].innerHTML = letters.toUpperCase();
            }
        }
    }
    makeAvatar();
</script>