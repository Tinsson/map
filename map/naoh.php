<?php
header('Content-type:text/html;charset=utf-8');
require_once('cls_db.php');
session_start();
$db = new db();


if (isset($_POST['action']) && trim($_POST['action']) == 'getMarkers') {
    $sql = "SELECT * FROM map_items ORDER BY id DESC";
    $res = $db->query($sql);
    foreach ($res as $k => $v) {
        $arr[$k] = $v;
    }
    echo json_encode($arr);
    exit;
}

if (isset($_POST['action']) && trim($_POST['action']) == 'ajaxSearch') {
    $keywords = trim($_POST['keywords']);
    addslashes($keywords);
    $sql = "SELECT * FROM map_items WHERE status=1 AND item_name LIKE '%".$keywords."%' ORDER BY id DESC";
    $res = $db->query($sql);
    foreach ($res as $k => $v) {
        $arr[$k] = $v;
    }
    echo json_encode($arr);
    exit;
}

if (isset($_POST['action']) && trim($_POST['action']) == 'ajaxDel') {
    $rec_id = intval($_POST['rec_id']);
    if ($rec_id) {
        $sql = "UPDATE map_items SET status=0 WHERE id=".$rec_id;
        $db->query($sql);
        $state = 1;
        $msg = '操作成功!';
    } else {
        $state = 0;
        $msg = 'Oops!操作失败!';
    }
    $d['state'] = $state;
    $d['msg'] = $msg;
    echo json_encode($d);
    exit;
}

if (isset($_POST['action']) && trim($_POST['action'] == 'ajaxEdit'))
{
    $rec_id = intval($_POST['rec_id']);
    $item_name = trim($_POST['item_name']);
    $item_detail = trim($_POST['item_detail']);
    $lat = floatval($_POST['lat']);
    $lng = floatval($_POST['lng']);
    if ($rec_id) {
        $item_name = addslashes($item_name);
        $item_detail = addslashes($item_detail);
        
        $sql = "UPDATE map_items SET item_name='".$item_name."', item_detail='".$item_detail."', lat=$lat, lng=$lng WHERE id=$rec_id";
        $db->query($sql);
        $state = 1;
        $msg = '操作成功!';
    } else {
        $state = 0;
        $msg = 'Oops!操作失败!';
    }
    $d['state'] = $state;
    $d['msg'] = $msg;
    echo json_encode($d);
    exit;
}

/*if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] != 1) {
    header('Location:login.html');
    exit;
}*/

if (isset($_POST['a']) && $_POST['a'] == 'add') {
    $item_name = trim($_POST['item_name']);
    $item_detail = trim($_POST['item_detail']);
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    if ($item_name && $lat && $lng) {
        $item_name = addslashes($item_name);
        $item_detail = addslashes($item_detail);
        $sql = "SELECT id FROM map_items WHERE item_name='".$item_name."' AND lat=$lat AND lng=$lng";
        $row = $db->get_one($sql);
        if (!$row) {
            $status = 1;
            $sql = "INSERT INTO map_items (lat, lng, item_name, item_detail, status) VALUES ($lat, $lng, '$item_name', '$item_detail', $status)";
            $db->query($sql);
        }
    }
    header('Location:naoh.php');
    exit;
}


$sql = "SELECT * FROM map_items WHERE status=1 ORDER BY id DESC";
$res = $db->query($sql);

?>

<html>
<head>
    <meta charset="utf-8">
    <title>地图修改列表页面</title>
    <link rel="stylesheet" type="text/css" href="./static/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./static/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./static/css/reset.css">
    <link rel="stylesheet/less" type="text/css" href="./static/css/main.less">
    <script src="./static/js/jquery.js"></script>
    <script src="./static/js/bootstrap.js"></script>
    <script src="./static/js/less.min.js"></script>
</head>

<body>
    <div class="adhead">
        <div class="adhdcon">
            <h1 class="hdtxt">管理页面</h1>
            <a href="letmein.php?q=1" class="out"><i class="fa fa-sign-out"> </i> 退出</a>
        </div>
    </div>
    <div class="adcenter">
        <div class="adleft">
            <h1 class="usercon">欢迎你，admin</h1>
            <ul>
                <li>
                    <i class="fa fa-home fa-fw"></i>
                    <span class="left_con">首页</span>
                </li>
                <!-- <li>
                    <i class="fa fa-credit-card fa-fw"></i>
                    <span class="left_con">哈哈哈</span>
                </li>
                <li>
                    <i class="fa fa-github fa-fw"></i>
                    <span class="left_con">哈哈哈</span>
                </li>
                <li>
                    <i class="fa fa-facebook fa-fw"></i>
                    <span class="left_con">哈哈哈哈</span>
                </li>
                <li>
                    <i class="fa fa-bell fa-fw"></i>
                    <span class="left_con">辣辣</span>
                </li>
                <li style="border-bottom:none">
                    <i class="fa fa-globe fa-fw"></i>
                    <span class="left_con">地地道道</span>
                </li> -->
            </ul>
        </div>
        <div class="adright">
            <div class="context">
                <div class="fixbox">
                    <h1 class="fix_head">新建地图信息</h1>
                    <form action="naoh.php" method="post">
                        <p class="fix_group">
                            <label for="item_name" class="fix_lb">项目名：</label>
                            <input id="item_name" class="fix_ipt" type="text" name="item_name" />
                        </p>
                        <p class="fix_group">
                            <label for="lng" class="fix_lb">东经：</label>
                            <input id="lng" class="fix_ipt" type="text" name="lng" />
                        </p>
                        <p class="fix_group">
                            <label for="lat" class="fix_lb">北纬：</label>
                            <input id="lat" class="fix_ipt" type="text" name="lat" />
                        </p>
                        <p class="fix_group clearfix">
                            <label for="detail" class="fix_lb" style="float:left">详情信息：</label>
                            <textarea id="detail" class="fix_txt" name="item_detail"></textarea>
                        </p>
                        <input class="fix_sub" type="submit" name="增加" />
                        <input type="hidden" name="a" value="add" />
                    </form>
                </div>
                <div class="listbox">
                    <h1 class="list_head">信息列表</h1>
                    <table class="list_tb">
                    <thead>
                        <tr>
                        <th>项目名</th>
                        <th>北纬</th>
                        <th>东经</th>
                        <th style="width: 180px">详情</th>
                        <th style="width: 200px;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res) { ?>
                            <?php foreach ($res as $k => $v) { ?>
                                <tr>
                                    <td><?php echo $v['item_name']; ?></td>
                                    <td><?php echo $v['lat']; ?></td>
                                    <td><?php echo $v['lng']; ?></td>
                                    <td class="text_td" title="<?php echo $v['item_detail']; ?>"><?php echo $v['item_detail']; ?></td>
                                    <td>
                                        <input type="hidden" class="rec_id" value="<?php echo $v['id'] ?>">
                                        <a href="javascript:;"  class="btn info editBtn" data-toggle="modal" data-target="#myModal">编辑</a>
                                        <a href="javascript:;" class="btn danger delBtn">删除</a>
                                        <input type="hidden" class="td_detail" value="<?php echo $v['item_detail'] ?>">
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>   
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                    <h4 class="modal-title edithd" id="myModalLabel">修改地图信息</h4>
                </div>
                <div class="modal-body">
                    <form id="edit_form" class="form-horizontal" role="form" action="">
                        <input type="hidden" id="edit_id" name="rec_id">
                        <div class="form-group">
                            <label for="editname" class="col-sm-2 control-label edtip">项目名：</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="editname" name="item_name" placeholder="请输入项目名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editlng" class="col-sm-2 control-label edtip">东经：</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="editlng" name="lng" placeholder="请输入东经信息">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editlat" class="col-sm-2 control-label edtip">北纬：</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="editlat" name="lat" placeholder="请输入北纬信息">
                            </div>
                        </div><div class="form-group">
                            <label for="editdetail" class="col-sm-2 control-label edtip">详情：</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="editdetail" rows="3" name="item_detail" placeholder="请输入详情"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-success" id="ed_commit">提交更改</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <script>
        $(".delBtn").click(function(){
            if(confirm("是否删除此信息？")){
                var rec_id = $(this).parent().find(".rec_id").val();
                var tr = $(this).parent().parent();
                $.ajax({
                    type: "post",
                    url: "naoh.php",
                    data: {action: "ajaxDel",rec_id: rec_id},
                    dataType: "json",
                    success: function(d){
                        if(d.state == 1){
                            alert(d.msg);
                            tr.remove();
                        }else{
                            alert(d.msg);
                        }
                    }
                })
            }
        })
        $(".editBtn").click(function(){
            var rec_id = $(this).parent().find(".rec_id").val();
            var detail = $(this).parent().find(".td_detail").val();
            var item_name = $(this).parent().parent().find("td").eq(0).text();
            var lat = $(this).parent().parent().find("td").eq(1).text();
            var lng = $(this).parent().parent().find("td").eq(2).text();
            $("#edit_id").val(rec_id);
            $("#editname").val(item_name);
            $("#editlat").val(lat);
            $("#editlng").val(lng);
            $("#editdetail").val(detail);
        })
        $("#ed_commit").click(function(){
            if(confirm("确认修改此信息？")){
                var rec_id = $("#edit_id").val(),
                    item_name = $("#editname").val(),
                    lat = $("#editlat").val(),
                    lng = $("#editlng").val(),
                    item_detail = $("#editdetail").val();
                $.ajax({
                    type: "post",
                    url: "naoh.php",
                    data: {action: "ajaxEdit",rec_id: rec_id,item_name: item_name,item_detail: item_detail,lat: lat,lng: lng},
                    dataType: "json",
                    success: function(d){
                        if(d.state == 1){
                            alert(d.msg);
                            window.location.reload();
                        }else{
                            alert(d.msg);
                            window.location.reload();
                        }
                    }
                })
            }
        })
    </script>
</body>
</html>