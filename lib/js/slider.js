    // Basic slider
    
    $('#basic-slider').gaBasicSlider({
        btnNext : $('#basic-slider-next'),
        btnPrevious : $('#basic-slider-previous'),
        indicators : $('#basic-slider-indicators')
    });
    
    // API
    
    $('#slider-start').on('click', function(){
        // start the slider
        $('#basic-slider').gaBasicSlider('start');
        
        // update alert info for this example
        $('#slider-alert').text('Animation is on.');
    });

    $('#slider-stop').on('click', function(){
        // stop the slider
        $('#basic-slider').gaBasicSlider('stop');
        
        // update alert info for this example
        $('#slider-alert').text('Animation has stoped.');
    });