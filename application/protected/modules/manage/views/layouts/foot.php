<div id="checkout-logout-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      
      <div class="modal-body">
        	<strong>确定退出吗？</strong>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="logout">确定</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var $logout = $("#logout");
$logout.on("click", function(e){
	$.ajax({
		url: "logout",
		dataType: "json",
		type: "POST",
		beforeSend: function(){$logout.attr("disabled", "disabled").text("退出中...")},
		success: function(data){
			if(data.code === 1){
				window.location.href="login.html";
			}else{
				$logout.text(data.msg);
			}
		}
	})
});
</script>
</body>
</html>
