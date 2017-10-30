var mapWidth=30;
var mapHeight=30;
var wallDencity=0.9;
var wallSize=4;
window.map=[[]];

function initMap()
{
    for (var i=0; i<mapHeight; i++) {
        row=[];
        for (var j=0; j<mapWidth; j++) {
            row[j]=0;
        }
        window.map[i]=row;
    }
    for (var i=0; i<mapHeight; i++) {
        for (var j=0; j<mapWidth; j++) {
            wall=Math.random()>wallDencity?9:0;
            if (wall==9)
            {
                thisWallSize=Math.random()*wallSize;
                thisWallSize2=Math.random()*wallSize;
                for (k=0;k<thisWallSize;k++)
                {
                    for (l=0;l<thisWallSize2;l++)
                    {
                        if (i+k<mapHeight && j+l<mapWidth)
                        {
                            window.map[i+k][j+l]=9;
                        }

                    }
                }
            }
            else
            {
                if (window.map[i][j]==0)
                {}
//                window.map[i][j]=0;
            }
        }
    }

    window.map[0][0]=1;
    window.map[mapHeight-1][mapWidth-1]=2;
}

function showMap()
{
    displayMap='';
    for (var i=0; i<mapHeight; i++) {
        displayRow='<div class="map-row">';
        for (var j=0; j<mapWidth; j++) {
            displayRow=displayRow+'<div class="point-'+(window.map[i][j]==9?'9-'+checkPoint(j,i):window.map[i][j])+'" data-x="'+j+'" data-y="'+i+'"></div>';
        }
        displayRow=displayRow+'</div>';
        displayMap=displayMap+displayRow;
    }
    $('#map').html(displayMap);
}

function checkPoint (x,y)
{
    return mapPoint(x,y-1)+mapPoint(parseInt(x)+1,y)*2+mapPoint(x,parseInt(y)+1)*4+mapPoint((x-1),y)*8;
}

function mapPoint (x,y)
{
    if (x<0 || x==mapWidth || y<0 || y==mapHeight)
    {
        return 0;
    }
    return (window.map[y][x]==9?1:0);
}

function setPoint (x,y)
{
    if (x<0 || x>mapWidth || y<0 || y==mapHeight)
    {
        return;
    }
    point=window.map[y][x]==9?9:0;
    pointClass=checkPoint(x,y);
    row=$('#map').children().eq(y);
    if (point==9)
    {
        row.children().eq(x).removeClass().addClass('point-9-'+pointClass);
    }
    else
    {
        row.children().eq(x).removeClass().addClass('point-' + window.map[y][x]);
    }
}

$(document).ready(function() {
    initMap();
    showMap();
    $('.map-row').on('click','div',function() {
        x=$(this).attr('data-x');
        y=$(this).attr('data-y');
        point=window.map[y][x]?0:9;
        window.map[y][x]=point;
        setPoint(x,y);
        setPoint(parseInt(x)+1,y);
        setPoint(x-1,y);
        setPoint(x,y-1);
        setPoint(x,parseInt(y)+1);
    });

    $('#send').click(function() {
        $.ajax({
            type: 'POST',
            url: '/process.php',
            dataType: 'json',
            data: {data: window.map},
            success: function (response) {
                if (response.result=='ok') {
                    $.each(response.route, function (key, value) {
                        y = parseInt(value / mapHeight);
                        x = parseInt(value % mapWidth);
                        row = $('#map').children().eq(y);
                        row.children().eq(x).removeClass().addClass('waypoint');
                    });
                }
            },
            error: function () {
            }
        });
        return false;
    });
});