<div class="row">
      <form class="form-horizontal" role="form" id="upload-form">
        <div class="form-group">
          <div class="col-sm-12">
             <h3 class="text-danger tracker-address">服务器tracker地址是：xxxx</h3>
          </div>
        </div>
        <div class="form-group">
          <label for="torrent-file" class="col-sm-2 control-label">种子</label>
          <div class="col-sm-10">
            <input type="file" class="form-control" id="torrent-file">
          </div>
        </div>
        <div class="form-group">
          <label for="mainTitle" class="col-sm-2 control-label">标题</label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="inputPassword3" placeholder="标题">
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">副标题</label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="inputPassword3" placeholder="副标题">
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">简介</label>
          <div class="col-sm-10">
            <script id="uecontainer" name="content" type="text/plain">
            </script>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10 submit-btn">
            <button type="submit" class="btn btn-primary" id="submit">发布</button>
          </div>
        </div>
      </form>
    </div>


  <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
  <script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.config2.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.all.min.js"></script>
  <script type="text/javascript">
  var ue = UE.getEditor('uecontainer');
  ue.ready(function() {
      ue.setContent('Hello Boy ！');
      //获取html内容，返回: <p>hello</p>
      var html = ue.getContent();
      //获取纯文本内容，返回: hello
      var txt = ue.getContentTxt();
  });
  </script>