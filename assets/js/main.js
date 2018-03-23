jQuery(document).ready(function ($) {

  var canvas = new fabric.Canvas('c', {
    selection: false
  });
  canvas.setHeight(window.innerHeight);
  canvas.setWidth($('#tremtr_main_metabox').width() - 24);
  canvas.defaultCursor = 'crosshair'

  var canvasObjectId = 0;

  var circle = '';
  var isDown = '';
  var originX = '';
  var originY = '';

  var currentElement = '';

  var mouseOverObject = null;

  var justCreated = null;

  var missedTables = [];

  var sortedTablesAfterLoad = [];


canvas.observe('mouse:down', function (o) {

  if (o.target != null) {

    var objId = o.target.id;
    var item = '';

    $.each(canvas.getObjects(), function (i) {

      if (Number(objId) === Number(canvas.item(i).id)) {
        item = i;
        return false;
      }
    });

    canvas.set({
      _activeObject: canvas.item(item)
    });

    canvas.forEachObject(function (o) {
      if (o.get('type') === 'rect') {
        o.setCoords();
        o.lockMovementX = false;
        o.lockMovementY = false;
        o.lockScalingX = false;
        o.lockScalingY = false;
        o.lockUniScaling = false;
        o.lockRotation = true;
        o.hasControls = true;
        o.hoverCursor = 'pointer';
        o.hasBorders = true;
        o.borderColor = 'red';
        o.cornerColor = 'green';
        o.cornerSize = 10;
        o.transparentCorners = false;
        o.hasRotatingPoint = false;
      } else {
        o.setCoords();
        o.lockMovementX = true;
        o.lockMovementY = true;
        o.lockScalingX = true;
        o.lockScalingY = true;
        o.lockUniScaling = true;
        o.lockRotation = true;
        o.hasControls = false;
        o.hoverCursor = 'pointer';
        o.hasBorders = false;
        o.selectable = false;
        o.borderColor = 'red';
        o.cornerColor = 'green';
        o.cornerSize = 10;
        o.transparentCorners = false;
        o.evented = false;
      }
    })

    canvas.renderAll();

    
  } else {

    isDown = true;
    var pointer = canvas.getPointer(o.e);
    origX = pointer.x;
    origY = pointer.y;

    var circleId = nextTableNumber()

    circle = new fabric.Rect({
      left: origX,
      top: origY,
      rx: 10,
      ry: 10,
      strokeWidth: 1,
      stroke: 'black',
      fill: 'transparent',
      selectable: true,
      name: [circleId, 1],
      id: circleId,
      lockMovementX: true,
      lockMovementY: true,
      lockScalingX: true,
      lockScalingY: true,
      lockUniScaling: true,
      lockRotation: true,
      hasControls: false,
      hoverCursor: 'pointer',
      hasBorders: false
    });

    canvas.add(circle);

    mouseOverObject = circle;
    justCreated = circle;

    canvas.renderAll();
  }
});

canvas.observe('mouse:move', function (o) {

  if (!(isDown)) return;
  var pointer = canvas.getPointer(o.e);

  if (origX > pointer.x) {
    justCreated.set({
      left: Math.abs(pointer.x)
    });
  }
  if (origY > pointer.y) {
    justCreated.set({
      top: Math.abs(pointer.y)
    });
  }

  justCreated.set({
    width: Math.abs(origX - pointer.x)
  });
  justCreated.set({
    height: Math.abs(origY - pointer.y)
  });

  justCreated.setCoords()


  canvas.renderAll();

});

canvas.on('mouse:up', function (o) {

  isDown = false;

  if ((circle.width < 30 || circle.height < 30) && circle === justCreated) {

    $.each(canvas.getObjects(), function (i) {

      if (Number(circle.id) === Number(canvas.item(i).id)) {

        addMissedTable(circle.id)

        canvas.remove(canvas.item(i));
        canvas.renderAll();

        return false;
      }
    });

  } else if (circle === justCreated) {

    var table = new fabric.Text(circle.name[0].toString(), {
      fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
      fontSize: 25,
      id: justCreated.id,
      top: circle.top + 10,
      left: circle.left + 10,
      selectable: false,
      lockMovementX: true,
      lockMovementY: true,
      lockScalingX: true,
      lockScalingY: true,
      lockUniScaling: true,
      lockRotation: true,
      hasControls: false,
      evented: false
    });

    var people = new fabric.Text(circle.name[1].toString(), {
      fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
      fontSize: 25,
      id: justCreated.id,
      top: circle.top,
      left: circle.left + 20,
      selectable: false,
      opacity: 0,
      lockMovementX: true,
      lockMovementY: true,
      lockScalingX: true,
      lockScalingY: true,
      lockUniScaling: true,
      lockRotation: true,
      hasControls: false,
      evented: false
    });

    canvas.add(table);
    canvas.add(people);

    var b = circle.name[1];
    var html = '<li id="' + circle.id + '"> <i class="fa fa-circle " style="color: white;"></i> <span class="table" >' + circle.name[0] + '</span>  <input type="number" name="people" min="1" step="1" value="' + b + '"> <input type="number" name="width" min="40" step="1" value="' + parseInt(circle.width) + '"> <input type="number" name="height" min="40" step="1" value="' + parseInt(circle.height) + '"> <button class="trem-remove" ><i class="fa fa-times" aria-hidden="true" style="color: #f00;"></button></li>';

    if ($("#control-items").children('li').length !== 0) {
      if (Number(circle.id) < Number($("#control-items").children('li')[0].id)) {
        $("#control-items").prepend(html)
      } else {
        $(html).insertAfter($("#control-items").find('#' + (Number(circle.id) - 1)));
      }
    } else {
      $("#control-items").append(html)
    }


    $(".trem-remove").click(function (event) {
      event.preventDefault();

      var objId = $(this).closest('li').attr('id');

      $(this).parent().remove();

      $.each(canvas.getObjects(), function (i) {

        if (Number(objId) === Number(canvas.item(i).id)) {
          canvas.remove(canvas.item(i + 2));
          canvas.remove(canvas.item(i + 1));
          canvas.remove(canvas.item(i));
          canvas.renderAll();

          addMissedTable(objId)

          return false;
        }
      });
    });

    $('input[name="people"]').change(function () {
      
      var objId = $(this).parent('li').attr('id');
      var newVal = $(this).val();

      $.each(canvas.getObjects(), function (i) {

        if (Number(objId) === Number(canvas.item(i).id)) {
          canvas.item(i + 2).text = newVal
          canvas.item(i).name[1] = Number(newVal);
          canvas.renderAll();
          return false;
        }
      });

    });


    $('input[name="width"]').change(function () {
      var objId = $(this).parent('li').attr('id');
      var newVal = $(this).val();
      $.each(canvas.getObjects(), function (i) {

        if (Number(objId) === Number(canvas.item(i).id)) {
          canvas.item(i).set({
            width: Number(newVal)
          });
          canvas.renderAll();
          return false;
        }
      });

    });

    $('input[name="height"]').change(function () {
      var objId = $(this).parent('li').attr('id');
      var newVal = $(this).val();
      $.each(canvas.getObjects(), function (i) {

        if (Number(objId) === Number(canvas.item(i).id)) {
          canvas.item(i).set({
            height: Number(newVal)
          });
          canvas.renderAll();
          return false;
        }
      });

    });

    justCreated = null;
  }
});


  //find next table number
  function nextTableNumber () {

    if (missedTables.length !== 0) {

      var next = missedTables[0]
      missedTables.shift()

      return next

    } else {

      var max = 0

      for (let i = 1; i < canvas.getObjects().length; i = i + 3) {
        var tableNumber = Number(canvas.item(i).text)
        if (max < tableNumber) {
          max = tableNumber
        }
      }

      return max + 1
    }
  }

  //create missedTables array after JSON parse (load)
  function createMissedTable () {

    var tables = [0]
    for (let i = 1; i < canvas.getObjects().length; i = i + 3) {
      tables.push(Number(canvas.item(i).text))
    }

    tables.sort(function (a, b) {
      return a - b
    });

    for (let i = 0; i < canvas.getObjects().length; i++) {
      var diff = Number(tables[i + 1]) - Number(tables[i]) - 1
      if (diff !== 0) {
        for (let j = 1; j <= diff; j++) {
          missedTables.push(tables[i] + j)
        }
      }

    }
  }

  //create sorted array of existed tables after JSON parse (load)
  function createSortedTables() {

    var tables = []
    for (let i = 1; i < canvas.getObjects().length; i = i + 3) {
      tables.push(Number(canvas.item(i).text))
    }

    tables.sort(function (a, b) {
      return a - b
    });

    sortedTablesAfterLoad = tables
  }

  //add next table number in missedTables array
  function addMissedTable(id) {
    var value = Number(id);

    missedTables.splice(_.sortedIndex(missedTables, value), 0, value);
  }


  // make correction
  $('html').keyup(function (e) {
    if (e.keyCode == 46) {
      if ((canvas.getObjects().length != 0) && (canvas.getActiveObject() != null)) {

        var objId = canvas.getActiveObject().id;
        var element = '#' + objId;
        $(element).remove();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            if ((mouseOverObject != null) && (mouseOverObject.id === canvas.item(i).id)) {
              mouseOverObject = null;
            }

            canvas.remove(canvas.item(i + 2));
            canvas.remove(canvas.item(i + 1));
            canvas.remove(canvas.item(i));
            canvas.renderAll();

            addMissedTable(objId)

            return false;
          }
        });
      }
    }
  });

  canvas.on('mouse:over', function (e) {

    if (e.target != null) {
      mouseOverObject = e.target;
    }
    canvas.renderAll();
  });

  canvas.on('mouse:out', function (e) {

    mouseOverObject = null
  });


  canvas.on('object:moving', function (e) {
    if (canvas.getActiveObject() != undefined) {

      var id = canvas.getActiveObject().id;

      $.each(canvas.getObjects(), function (i) {

        if (!(canvas.item(i).id === undefined)) {
          if (Number(id) === Number(canvas.item(i).id)) {

            canvas.item(i + 2).top = canvas.getActiveObject().top;
            canvas.item(i + 2).left = canvas.getActiveObject().left;

            canvas.item(i + 1).top = canvas.getActiveObject().top + 10;
            canvas.item(i + 1).left = canvas.getActiveObject().left + 10;

            canvas.renderAll();

            return false;
          }
        }
      });

      canvas.renderAll();
    }
  });


  canvas.on('object:scaling', function (e) {

    if (canvas.getActiveObject() != undefined) {

      var id = canvas.getActiveObject().id;

      $.each(canvas.getObjects(), function (i) {

        if (!(canvas.item(i).id === undefined)) {
          if (Number(id) === Number(canvas.item(i).id)) {

            canvas.item(i + 2).top = canvas.getActiveObject().top;
            canvas.item(i + 2).left = canvas.getActiveObject().left;

            canvas.item(i + 1).top = canvas.getActiveObject().top + 10;
            canvas.item(i + 1).left = canvas.getActiveObject().left + 10;

            canvas.renderAll();

            return false;
          }
        }
      });
      canvas.renderAll();
    }
  });

  canvas.on('object:modified', function (e) {

    if (canvas.getActiveObject() != undefined) {


      canvas.getActiveObject().set({
        height: Math.round(canvas.getActiveObject().scaleY * canvas.getActiveObject().height)
      });
      canvas.getActiveObject().set({
        width: Math.round(canvas.getActiveObject().scaleX * canvas.getActiveObject().width)
      });
      canvas.getActiveObject().set({
        scaleX: 1
      });
      canvas.getActiveObject().set({
        scaleY: 1
      });
      canvas.renderAll();


      var objId = canvas.getActiveObject().id;
      var newValWidth = canvas.getActiveObject().width;
      var newValHeight = canvas.getActiveObject().height;

      var str = "#" + Number(objId);
      $(str).children('input[name="width"]').val(Number(newValWidth));
      $(str).children('input[name="height"]').val(Number(newValHeight));
    }

  });




  canvas.on('object:selected', function (e) {
    if (currentElement) {
      currentElement.css("color", "white");
    }
    var id = canvas.getActiveObject().id;
    var li = "#" + id.toString();
    currentElement = $(li).children("i");
    currentElement.css("color", "green");

  });


  canvas.on('selection:cleared', function (e) {
    if (currentElement != "") {
      currentElement.css("color", "white");
    }
  });



  $(function () {
    $.contextMenu({
      selector: '.context-menu-one',
      trigger: 'right',
      hideOnSecondTrigger: true,
      reposition: false,
      callback: function (key, options) {
        if (key === 'delete') {
          $('html').trigger(jQuery.Event('keyup', {
            which: 46,
            keyCode: 46
          }));
        }
      },
      items: {
        "delete": {
          name: "Delete",
          icon: "delete",
          disabled: function () {


            if (mouseOverObject != null) {

              var objId = mouseOverObject.id;
              var item = '';

              $.each(canvas.getObjects(), function (i) {

                if (Number(objId) === Number(canvas.item(i).id)) {
                  item = i;
                  return false;
                }
              });

              canvas.set({
                _activeObject: canvas.item(item)
              });
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


  if (typeof wp != 'undefined' && typeof wp.media != 'undefined') {
    var featuredImage = wp.media.featuredImage.frame();
    featuredImage.on('select', function () {
      var attachment = featuredImage.state().get('selection').first().toJSON();

      var newScale = 1;
      canvas.setWidth($('#tremtr_main_metabox').width() - 24);
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

  if ($('#tremtr_content').val() != '') {
    canvas.loadFromJSON($('#tremtr_content').val(), function () {

      var objCounter = 1;
      var counter = 0;

      createMissedTable()
      createSortedTables()

      for (let k = 0; k < sortedTablesAfterLoad.length; k++) {
        $.each(canvas.getObjects(), function (i) {
          if (canvas.item(i).get('type') === 'rect' && canvas.item(i + 1).text == sortedTablesAfterLoad[k]) {

            var tablePeople = [canvas.item(i + 1).text, canvas.item(i + 2).text]
            canvas.item(i).name = tablePeople;

            canvas.item(i).id = canvas.item(i+1).text;
            canvas.item(i + 1).id = canvas.item(i + 1).text;
            canvas.item(i + 2).id = canvas.item(i + 1).text;

            var html = '<li id="' + canvas.item(i).id + '"> <i class="fa fa-circle " style="color: white;"></i> <span class="table" >' + canvas.item(i).name[0] + '</span> <input type="number" name="people" min="1" step="1" value="' + canvas.item(i).name[1] + '"> <input type="number" name="width" min="40" step="1" value="' + parseInt(canvas.item(i).width) + '"> <input type="number" name="height" min="40" step="1" value="' + parseInt(canvas.item(i).height) + '"> <button class="trem-remove"><i class="fa fa-times" aria-hidden="true" style="color: #f00;"></button></li>';
            $("#control-items").append(html)

            $(".trem-remove").click(function (event) {
              event.preventDefault();

              var objId = $(this).parent('li').attr('id');

              $(this).parent().remove();

              $.each(canvas.getObjects(), function (i) {

                if (Number(objId) === Number(canvas.item(i).id)) {
                  canvas.remove(canvas.item(i + 2));
                  canvas.remove(canvas.item(i + 1));
                  canvas.remove(canvas.item(i));
                  canvas.renderAll();

                  addMissedTable(objId)

                  return false;
                }
              });
            });
          }
        })
        
      }


      $('input[name="people"]').change(function () {
        var objId = $(this).parent('li').attr('id');
        var newVal = $(this).val();

        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i + 2).text = newVal;
            canvas.item(i).name[1] = Number(newVal);
            canvas.renderAll();
            return false;
          }
        });

      });


      $('input[name="width"]').change(function () {
        var objId = $(this).parent('li').attr('id');
        var newVal = $(this).val();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i).set({
              width: Number(newVal)
            });
            canvas.renderAll();
            return false;
          }
        });

      });

      $('input[name="height"]').change(function () {
        var objId = $(this).parent('li').attr('id');
        var newVal = $(this).val();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            canvas.item(i).set({
              height: Number(newVal)
            });
            canvas.renderAll();
            return false;
          }
        });

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

        canvas.setWidth($('#tremtr_main_metabox').width() - 24);

        canvas.setHeight(window.innerHeight);
      }


    });
  }



  $('form#post').submit(function (e) {

    $('#tremtr_content').val(JSON.stringify(canvas));


  });

  //prevent enter
  $("#control-items").bind("keypress", function (e) {
    if (e.keyCode == 13) {
      return false;
    }
  });

});
