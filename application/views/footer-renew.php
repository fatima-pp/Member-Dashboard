
  <!-- Compiled and minified JavaScript -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
  <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
  <script src="https://anijs.github.io/lib/anijs/anijs.js"></script>
  <script src="https://anijs.github.io/lib/anijs/helpers/dom/anijs-helper-dom-min.js"></script>
  
  <script>
    $(document).ready(function(){
        $(".dropdown-trigger").dropdown();

        var sidebar_elem = document.querySelector('.sidenav-close');
        var sidebar_inst = M.Sidenav.getInstance(sidebar_elem);

        $('.sidenav').sidenav(
            {
                edge:'right',
                inDuration:300
            }
        );


        $('.materialboxed').materialbox();
        $('.parallax').parallax();
        $('.tabs').tabs();
        $('.datepicker').datepicker({
            disableWeekends:true
        });
        $('.tooltipped').tooltip();
        $('.scrollspy').scrollSpy();
        $('select').formSelect();

        var elem = document.querySelector('.tabs');
        var options = {}
        var instance = M.Tabs.init(elem, options);


        // $(".sidenav-trigger").hover(
        //     function() {  
        //         sidebar_inst.open();
        //     },
        //     setTimeout(()=>{
        //         sidebar_inst.close();
        //     },1000)
        // );

    });

  </script>


</body>
</html>