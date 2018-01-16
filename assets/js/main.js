jQuery(document).ready(function ($) {

  var canvas = new fabric.Canvas('c', { selection: false });
  canvas.setHeight(window.innerHeight);
  canvas.setWidth($('#tremtr_main_metabox').width()-24);

  var canvasObjectId = 0;

  var circle = '';
  var isDown = '';
  var originX = '';
  var originY = '';

  var state = false;

  var currentElement = '';

  var mouseOverObject = null;

  // initial state 

  $( document ).ready(function() {
    $('#add').trigger('click');
  });

  // toogle button

  $("#edit").click(function(){
      if (state) {

        canvas.defaultCursor = 'crosshair';

        canvas.isDrawingMode = false;
        canvas.off('mouse:down');
        canvas.off('mouse:move');
        canvas.off('mouse:up');

        canvas.forEachObject(function(o){ 
          if (o.get('type') === 'circle') {
            o.setCoords();
            o.lockMovementX = false; 
            o.lockMovementY = false; 
            o.lockScalingX = false;
            o.lockScalingY = false;
            o.lockRotation = false; 
            o.hasControls = true;
            o.hoverCursor = 'move';
            o.hasBorders = true;
            o.borderColor = 'red';
            o.cornerColor = 'green';
            o.cornerSize = 10;
            o.transparentCorners = false;
          } else {
            o.setCoords();
            o.lockMovementX = false; 
            o.lockMovementY = false; 
            o.lockScalingX = false;
            o.lockScalingY = false;
            o.lockUniScaling = false;
            o.lockRotation = false; 
            o.hasControls = true;
            o.hoverCursor = 'pointer';
            o.hasBorders = true;
            o.borderColor = 'red';
            o.cornerColor = 'green';
            o.cornerSize = 10;
            o.transparentCorners = false;
          }
        })

        canvas.observe('mouse:down', function(o){

          if (mouseOverObject === null) {

            $('#add').trigger('click');
            
            return false; 
          }
        })
      }
  })

  $("#add").click(function(){

    if (!state) {
      canvas.defaultCursor = 'crosshair';

      canvas.forEachObject(function(o){ 
        o.lockMovementX = true; 
        o.lockMovementY = true; 
        o.lockScalingX = true;
        o.lockScalingY = true;
        o.lockUniScaling = true;
        o.lockRotation = true; 
        o.hasControls = false;
        o.hoverCursor = 'pointer';
        o.hasBorders = false;
      })

      canvas.observe('mouse:down', function(o){

        if ((mouseOverObject != null) && (mouseOverObject.containsPoint(canvas.getPointer(o.e)))){

          $('#edit').trigger('click');

          var objId = mouseOverObject.id;
          var item = '';

          $.each(canvas.getObjects(), function (i) {

            if (Number(objId) === Number(canvas.item(i).id)) {
              item = i + 4;
              return false; 
            }
          });

          canvas.set({_activeObject:canvas.item(item)});
          canvas.renderAll();

          return false; 
        }

        isDown = true;
        var pointer = canvas.getPointer(o.e);
        origX = pointer.x;
        origY = pointer.y;
        circle = new fabric.Rect({
          left: pointer.x,
          top: pointer.y,
          width: 50,
          height: 50,
          rx: 10,
          ry: 10,
          strokeWidth: 1,
          stroke: 'black',
          fill: 'transparent',
          selectable: true,
          originX: 'center', 
          originY: 'center',
          name: [canvasObjectId+1, 1],
          id: canvasObjectId,
          lockMovementX: true,
          lockMovementY : true,
          lockScalingX : true,
          lockScalingY : true,
          lockUniScaling : true,
          lockRotation : true,
          hasControls: false,
          hoverCursor: 'pointer',
          hasBorders: false
        });

        var table = new fabric.Text(circle.name[0].toString(), {
          fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
          fontSize: 25, 
          id: canvasObjectId,
          top: circle.top-15,
          left: circle.left-7.5,
          selectable: false
        });

        var icon_table = new fabric.Text("", {
          fontFamily: "FontAwesome",
          fontSize: 25, 
          top: circle.top-15,
          left: circle.left-circle.width/2,
          selectable: false,
          id: canvasObjectId
        });

        var people = new fabric.Text(circle.name[1].toString(), {
          fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
          fontSize: 15, 
          id: canvasObjectId,
          top: circle.top+circle.height/2,
          left: circle.left-5,
          selectable: false
        });

        var icon_people = new fabric.Text("", {
          fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
          fontSize: 15, 
          top: circle.top+circle.height/2,
          left: circle.left-20,
          selectable: false,
          id: canvasObjectId
        });

        canvas.add(table);
        canvas.add(people);
        canvas.add(icon_table);
        canvas.add(icon_people);
        canvas.add(circle);


        mouseOverObject = circle;
        canvas.renderAll();

        var b = circle.name[1];
        var html = '<li> <i class="fa fa-circle " style="color: white;"></i> <input type="number" name="table" min="1" step="1" value="' + circle.name[0] + '">  <input type="number" name="people" min="1" step="1" value="' + b + '"> <input type="number" name="width" min="40" step="1" value="' + circle.width + '"> <input type="number" name="height" min="40" step="1" value="' + circle.height + '"> <button class="trem-remove" id="'+circle.id+'"><i class="fa fa-times" aria-hidden="true" style="color: #f00;"></button></li>';
         $( "#control-items" ).append( html );

        canvasObjectId++;

        $(".trem-remove").click(function( event ) {
          event.preventDefault();

          var objId = this.id;

          $( this ).parent().remove();

          $.each(canvas.getObjects(), function (i) {

            if (Number(objId) === Number(canvas.item(i).id)) {
              canvas.remove(canvas.item(i+4));
              canvas.remove(canvas.item(i+3));
              canvas.remove(canvas.item(i+2));
              canvas.remove(canvas.item(i+1));
              canvas.remove(canvas.item(i));
              canvas.renderAll();
              return false; 
            }
          });
        });

        $('input[name="table"]').change(function () {
          var objId = $(this).siblings("button").attr('id');
          var newVal = $(this).val();

          $.each(canvas.getObjects(), function (i) {

            if (Number(objId) === Number(canvas.item(i).id)) {
              canvas.item(i).text = newVal;
              canvas.item(i+4).name[0] = Number(newVal);
              canvas.renderAll();
              return false; 
            }
          });
          $('#edit').trigger('click');
        });


        $('input[name="people"]').change(function () {
          var objId = $(this).siblings("button").attr('id');
          var newVal = $(this).val();

          $.each(canvas.getObjects(), function (i) {

            if (Number(objId) === Number(canvas.item(i).id)) {
              canvas.item(i+1).text = newVal;
              canvas.item(i+4).name[1] = Number(newVal);
              canvas.renderAll();
              return false; 
            }
          });
          $('#edit').trigger('click');
        });
     

        $('input[name="width"]').change(function () {
          var objId = $(this).siblings("button").attr('id');
          var newVal = $(this).val();
          $.each(canvas.getObjects(), function (i) {

            if (Number(objId) === Number(canvas.item(i).id)) {
              canvas.item(i+4).set({width: Number(newVal)});
              canvas.renderAll();
              return false; 
            }
          });
          $('#edit').trigger('click');
        });

        $('input[name="height"]').change(function () {
          var objId = $(this).siblings("button").attr('id');
          var newVal = $(this).val();
          $.each(canvas.getObjects(), function (i) {

            if (Number(objId) === Number(canvas.item(i).id)) {
              canvas.item(i+4).set({height: Number(newVal)});
              canvas.renderAll();
              return false; 
            }
          });
          $('#edit').trigger('click');
        });
      });

      canvas.observe('mouse:move', function(o){
        if (!( isDown)) return;
        var pointer = canvas.getPointer(o.e);
        //circle.set({ radius: Math.abs(origX - pointer.x - 40) });
        canvas.renderAll();
      });

      canvas.on('mouse:up', function(o){
        isDown = false;
      });
    }
  });

  $("#edit").click(function(){
    if (state === true) {
      state = !state;
    }
  });

  $("#add").click(function(){
    if (state === false) {
      state = !state;
    }
  });




  // make correction
  $('html').keyup(function(e){
    if(e.keyCode == 46) {
      if ((canvas.getObjects().length != 0) && (canvas.getActiveObject() != null)) {

        var objId = canvas.getActiveObject().id;
        var element = '#'+ objId;
        $( element ).parent().remove();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            if ((mouseOverObject != null) && (mouseOverObject.id === canvas.item(i).id)) {
              mouseOverObject = null;
            }

            canvas.remove(canvas.item(i+4));
            canvas.remove(canvas.item(i+3));
            canvas.remove(canvas.item(i+2));
            canvas.remove(canvas.item(i+1));
            canvas.remove(canvas.item(i));
            canvas.renderAll();

            return false; 
          }
        });
      }
    }
  });

  canvas.on('mouse:over', function(e) {

    if (e.target != null) {
      mouseOverObject = e.target;
    }
    canvas.renderAll();
  });

  canvas.on('mouse:out', function(e) {

    mouseOverObject = null
  });


  canvas.on('object:moving', function(e) {
    if (canvas.getActiveObject() != undefined) {

      var id = canvas.getActiveObject().id;

      $.each(canvas.getObjects(), function (i) {

        if (!(canvas.item(i).id === undefined)) {
          if (Number(id) === Number(canvas.item(i).id)) {

            canvas.item(i+3).top = canvas.getActiveObject().top+canvas.getActiveObject().height/2;
            canvas.item(i+3).left = canvas.getActiveObject().left-20;

            canvas.item(i+2).top = canvas.getActiveObject().top-15;
            canvas.item(i+2).left = canvas.getActiveObject().left-20;

            canvas.item(i+1).top = canvas.getActiveObject().top+canvas.getActiveObject().height/2;
            canvas.item(i+1).left = canvas.getActiveObject().left-5;
            
            canvas.item(i).top = canvas.getActiveObject().top-15;
            canvas.item(i).left = canvas.getActiveObject().left-7.5;

            canvas.renderAll();

            return false; 
          }
        }
      });

      canvas.renderAll();
    }
  });


  canvas.on('object:scaling', function(e) {

    if (canvas.getActiveObject() != undefined) {

      var id = canvas.getActiveObject().id;

      $.each(canvas.getObjects(), function (i) {

        if (!(canvas.item(i).id === undefined)) {
          if (Number(id) === Number(canvas.item(i).id)) {

            canvas.item(i+3).top = canvas.getActiveObject().top+canvas.getActiveObject().height / 2 * canvas.getActiveObject().scaleY;
            canvas.item(i+3).left = canvas.getActiveObject().left-20;

            canvas.item(i+2).top = canvas.getActiveObject().top-15;
            canvas.item(i+2).left = canvas.getActiveObject().left-20;

            canvas.item(i+1).top = canvas.getActiveObject().top+canvas.getActiveObject().height/2 * canvas.getActiveObject().scaleY;
            canvas.item(i+1).left = canvas.getActiveObject().left-5;
            
            canvas.item(i).top = canvas.getActiveObject().top-15;
            canvas.item(i).left = canvas.getActiveObject().left-7.5;

            canvas.renderAll();

            return false; 
          }
        }
      });
      canvas.renderAll();
    }
  });

  canvas.on('object:modified', function(e) {

    if (canvas.getActiveObject() != undefined) {

      canvas.getActiveObject().set({ height: Math.round(canvas.getActiveObject().scaleY * canvas.getActiveObject().height)  });
      canvas.getActiveObject().set({ width: Math.round(canvas.getActiveObject().scaleX * canvas.getActiveObject().width)  });
      canvas.getActiveObject().set({ scaleX: 1  });
      canvas.getActiveObject().set({ scaleY: 1  });
      canvas.renderAll();


      var objId = canvas.getActiveObject().id;
      var newValWidth = canvas.getActiveObject().width;
      var newValHeight = canvas.getActiveObject().height;

      var str = "#" + Number(objId);
      $(str).siblings('input[name="width"]').val(Number(newValWidth));
      $(str).siblings('input[name="height"]').val(Number(newValHeight));
    }

  });




  canvas.on('object:selected', function(e) {
    if (currentElement) {
      currentElement.css("color", "white");
    }
    var id = canvas.getActiveObject().id;
    var button = "#" + id.toString();
    currentElement = $(button).siblings("i");
    currentElement.css("color", "green");

  });


  canvas.on('selection:cleared', function(e) {
     if (currentElement != "") {
      currentElement.css("color", "white");
    }
  });



  $(function() {
      $.contextMenu({
          selector: '.context-menu-one', 
          trigger: 'right',
          hideOnSecondTrigger: true,
          reposition: false,
          callback: function(key, options) {
            if (key === 'delete') {
              $('html').trigger(jQuery.Event('keyup', {which: 46, keyCode: 46}));
            }
          },
          items: {
            "delete": {
              name: "Delete",
              icon: "delete",
              disabled: function(){


                if (mouseOverObject != null) {

                  var objId = mouseOverObject.id;
                  var item = '';

                  $.each(canvas.getObjects(), function (i) {

                    if (Number(objId) === Number(canvas.item(i).id)) {
                      item = i + 4;
                      return false; 
                    }
                  });

                  canvas.set({_activeObject:canvas.item(item)});
                  canvas.renderAll();
                }
                
                if (mouseOverObject === null) {
                  return true; 
                } else {
                  return false; 
                }
              }
            }
          }
      });
  });


  if(typeof wp != 'undefined' && typeof wp.media != 'undefined') {
    var featuredImage = wp.media.featuredImage.frame();
    featuredImage.on('select', function () {
      var attachment = featuredImage.state().get('selection').first().toJSON();

      var newScale = 1;
      canvas.setWidth( $('#tremtr_main_metabox').width()-24);
      if (attachment.width > canvas.width) {
        newScale = canvas.width / Number(attachment.width);
      } else {
        canvas.setWidth(Number(attachment.width));
      }


      canvas.setBackgroundImage(attachment.url, canvas.renderAll.bind(canvas), {
        opacity: 1,
        stretch: false,
        scaleX: newScale,
        scaleY: newScale
      });

      canvas.setHeight(newScale * Number(attachment.height));
    });
  }

  if($('#tremtr_content').val() != ''){
    canvas.loadFromJSON($('#tremtr_content').val(), function () {

      var objCounter = 1;
      var counter = 0;

      $.each(canvas.getObjects(), function (i) {

        canvas.item(i).id = canvasObjectId;

        counter ++;
        if (counter%5 === 0) {

          var tablePeople = [canvas.item(i-4).text, canvas.item(i-3).text]
          canvas.item(i).name = tablePeople;

          canvasObjectId++;

          var html = '<li> <i class="fa fa-circle " style="color: white;"></i> <input type="number" name="table" min="1" step="1" value="' + canvas.item(i).name[0] + '">  <input type="number" name="people" min="1" step="1" value="' + canvas.item(i).name[1] + '"> <input type="number" name="width" min="40" step="1" value="' + canvas.item(i).width + '"> <input type="number" name="height" min="40" step="1" value="' + canvas.item(i).height + '"> <button class="trem-remove" id="'+canvas.item(i).id+'"><i class="fa fa-times" aria-hidden="true" style="color: #f00;"></button></li>';
          $( "#control-items" ).append( html );

          $(".trem-remove").click(function( event ) {
            event.preventDefault();

            var objId = this.id;

            $( this ).parent().remove();

            $.each(canvas.getObjects(), function (i) {

              if (Number(objId) === Number(canvas.item(i).id)) {
                canvas.remove(canvas.item(i+4));
                canvas.remove(canvas.item(i+3));
                canvas.remove(canvas.item(i+2));
                canvas.remove(canvas.item(i+1));
                canvas.remove(canvas.item(i));
                canvas.renderAll();
                return false; 
              }
            });
          });
        }
      })

      $('input[name="table"]').change(function () {
        var objId = $(this).siblings("button").attr('id');
        var newVal = $(this).val();

        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i).text = newVal;
            canvas.item(i+4).name[0] = Number(newVal);
            canvas.renderAll();
            return false; 
          }
        });
        $('#edit').trigger('click');
      });


      $('input[name="people"]').change(function () {
        var objId = $(this).siblings("button").attr('id');
        var newVal = $(this).val();

        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i+1).text = newVal;
            canvas.item(i+4).name[1] = Number(newVal);
            canvas.renderAll();
            return false; 
          }
        });
        $('#edit').trigger('click');
      });
   

      $('input[name="width"]').change(function () {
        var objId = $(this).siblings("button").attr('id');
        var newVal = $(this).val();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i+4).set({width: Number(newVal)});
            canvas.renderAll();
            return false; 
          }
        });
        $('#edit').trigger('click');
      });

      $('input[name="height"]').change(function () {
        var objId = $(this).siblings("button").attr('id');
        var newVal = $(this).val();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i+4).set({height: Number(newVal)});
            canvas.renderAll();
            return false; 
          }
        });
        $('#edit').trigger('click');
      });

      var newScale = 1;
      if (canvas.backgroundImage !== null) {
        if (canvas.backgroundImage.width > canvas.width) {
          newScale = canvas.width / Number(canvas.backgroundImage.width);
        } else {
          canvas.setWidth(Number(canvas.backgroundImage.width));
        }

        canvas.setHeight(newScale * Number(canvas.backgroundImage.height));
      } else {

        canvas.setWidth($('#tremtr_main_metabox').width()-24);

        canvas.setHeight(window.innerHeight);
      }
      

    });
  }



  $('form#post').submit(function (e) {
  
      $('#tremtr_content').val(JSON.stringify(canvas));


  });

});





// console.log(JSON.stringify(canvas)); json


// var canvas = new fabric.Canvas('c', { selection: false });

// canvas.loadFromJSON('{"objects":[{"type":"circle","originX":"center","originY":"center","left":709.59,"top":508.99,"width":119.76,"height":119.76,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":59.88092161211887,"startAngle":0,"endAngle":6.283185307179586},{"type":"circle","originX":"center","originY":"center","left":406.2,"top":645.72,"width":107.79,"height":107.79,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":53.89282945090696,"startAngle":0,"endAngle":6.283185307179586},{"type":"circle","originX":"center","originY":"center","left":273.46,"top":557.89,"width":73.85,"height":73.85,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":36.92656832747332,"startAngle":0,"endAngle":6.283185307179586},{"type":"circle","originX":"center","originY":"center","left":147.71,"top":347.31,"width":9.98,"height":9.98,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":4.990076801009906,"startAngle":0,"endAngle":6.283185307179586},{"type":"circle","originX":"center","originY":"center","left":154.7,"top":313.38,"width":181.64,"height":181.64,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":90.81939777838028,"startAngle":0,"endAngle":6.283185307179586},{"type":"circle","originX":"center","originY":"center","left":289.43,"top":262.48,"width":95.81,"height":95.81,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":47.90473728969505,"startAngle":0,"endAngle":6.283185307179586},{"type":"circle","originX":"center","originY":"center","left":352.3,"top":253.5,"width":31.94,"height":31.94,"fill":"transparent","stroke":"black","strokeWidth":1,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"radius":15.968245763231664,"startAngle":0,"endAngle":6.283185307179586}],"backgroundImage":{"type":"image","originX":"left","originY":"top","left":0,"top":0,"width":1089,"height":782,"fill":"rgb(0,0,0)","stroke":null,"strokeWidth":0,"strokeDashArray":null,"strokeLineCap":"butt","strokeLineJoin":"miter","strokeMiterLimit":10,"scaleX":1,"scaleY":1,"angle":0,"flipX":false,"flipY":false,"opacity":1,"shadow":null,"visible":true,"clipTo":null,"backgroundColor":"","fillRule":"nonzero","globalCompositeOperation":"source-over","transformMatrix":null,"skewX":0,"skewY":0,"crossOrigin":"","cropX":0,"cropY":0,"src":"http://localhost:3000/images/Restaurant-Floor-Plans.png","filters":[]}}', function() {
//   canvas.renderAll();
//   $.each(canvas.getObjects(), function (i) {
//     $( "ul" ).append( '<li> <input type="number" name="table"> <input type="number" name="people"> <button type="button">Remove</button></li>' );
//   });

//   $("button").click(function(){
//     $( this ).parent().remove();
  
//   });
// });

// var list = document.getElementById('items');





