<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<style type="text/css">
		body, html{width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
		#allmap{height:100%;width:100%;}
		#r-result{width:100%;}
	</style>
    <link rel="stylesheet" type="text/css" href="./static/css/reset.css">
    <link rel="stylesheet/less" type="text/css" href="./static/css/main.less">
    <script src="./static/js/jquery.js"></script>
    <script src="./static/js/less.min.js"></script>
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=cU1mt0H6eDeT7stCr52HWzOyjH00dIQg"></script>
	<title>Map_application</title>
</head>
<body>
    <div class="secbox">
        <input id="sec_ipt" type="text" placeholder="请输入地点名称搜索">
        <button id="sec_sub"></button>
        <ul class="drop">
            <li class="drop_li">未搜索到对应地点</li>
            <li class="drop_li"><img src="./static/images/point.png" alt="">港岛路128号</li>
        </ul>
    </div>
	<div id="allmap"></div>
</body>
</html>
<script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
	// 百度地图API功能
    
    var map = new BMap.Map("allmap");
	var point = new BMap.Point(122.11, 30.06);
    map.enableScrollWheelZoom();
    map.centerAndZoom(point, 13);
    var myIcon = new BMap.Icon("./static/images/mark1.png", new BMap.Size(30,36));
    var marker2 = new BMap.Marker(point,{icon:myIcon});
    var opts = {};
    var markarr = [];
    $.ajax({
        url:'naoh.php',
        type:'POST',
        data:{action:'getMarkers'},
        dataType:'json',
        success:function(d) {
            data = d;
            
            opts = {
                width: 250,
                hegith: 80,
                title: '当前信息',
                enableMessage: true
            };
            for (var i=0;i<data.length;i++) {
                var n = '';
                n = 'marker'+(i+1);
                n = new BMap.Marker(new BMap.Point(data[i].lng, data[i].lat),{icon:myIcon}); // 创建点
                map.addOverlay(n);
                n.setTitle(data[i].id);
                var content = data[i].item_name;
                var label = new BMap.Label(data[i].item_name,{offset:new BMap.Size(20,-10)});
                n.setLabel(label);
                label.setStyle({
                    padding: "2px 5px",
                    color: "#23a0ff",
                    border: "0",
                    fontSize: "14px",
                    backgroundColor: "#FFF",
                    borderRadius: "5px"
                });
                addClickHandler(content, n);
                markarr.push(n);
            }
        }
    })
    
    function addClickHandler(content, marker)
    {
        marker.addEventListener("click", function(e){
            openInfo(content, e);
        });
    }
    
    function openInfo(content, e)
    {
        var p = e.target;
        var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
        var infoWindow = new BMap.InfoWindow(content, opts);
        map.openInfoWindow(infoWindow, point);
    }
    function throttle(method, context){
        clearTimeout(method.tId);
        method.tId = setTimeout(function(){
            method.call(context);
        },100);
    }
    function connect(){
        var value = $("#sec_ipt").val();
        if(value == ""){
            $(".drop").hide();
            return false;
        }
        $.ajax({
            type: "post",
            url: "naoh.php",
            data: {action: "ajaxSearch",keywords: value},
            dataType: "json",
            success: function(d){
                var li = "";
                if(d == ""){
                    li += '<li class="drop_li">未搜索到对应地点</li>';
                }else{
                    for(var i in d){
                        li += '<li class="drop_li" data-lng="'+d[i].lng+'" data-lat="'+d[i].lat+'"><img src="./static/images/point.png" alt="">';
                        li += d[i].item_name;
                        li += '</li>'
                    }
                }
                $(".drop").show();
                $(".drop").html(li);
                liclick();
            },
            error: function(){
                var li = '<li class="drop_li">未搜索到对应地点</li>';
                $(".drop").html(li);
                $(".drop").show();
            }
        })
    }
    function liclick(){
        return $(".drop_li").click(function(){
            var lng = $(this).data("lng"),
                lat = $(this).data("lat");
            var point = new BMap.Point(lng, lat);
            map.setCenter(point);
            var t = setTimeout("bmpclick("+lng+","+lat+")",50);
        })
    }


    $("#sec_ipt").on("input propertychange",function(){
        if($(this).val() == ""){
            $(".drop").hide();
        }else{
            throttle(connect);
        }
    })
    function bmpclick(lng,lat){
        for(var i in markarr){
            var mlng = markarr[i].getPosition().lng;
                mlat = markarr[i].getPosition().lat;
            if(mlng == lng && mlat == lat){
                $(".BMap_Marker")[i].click();
            }
        }
    }

</script>
