<?php if (!defined('THINK_PATH')) exit();?><input type="hidden" id="roleid" value="<?php echo ($roleinfo["id"]); ?>" />
<table class="node_table" width="100%">
    <tr>
        <td><span style="margin-left: 20px"><input type="checkbox" class="select_all" />全选</span></td>
    </tr>
    <?php if(is_array($nodetree)): $i = 0; $__LIST__ = $nodetree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="pnode_<?php echo ($vo["id"]); ?> cnode2_<?php echo ($vo["id"]); ?> cnode3_<?php echo ($vo["id"]); ?>" data-id="<?php echo ($vo["id"]); ?>" rid="<?php echo ($vo["id"]); ?>">
            <td><span style="margin-left: <?php echo ($vo['level']*40+40); ?>px"><input type="checkbox" level="1" class="node_check check-level1" value="<?php echo ($vo["id"]); ?>" <?php if($vo['hv'] == 1): ?>checked<?php endif; ?> /><?php echo ($vo["title"]); ?></span></td>
        </tr>
        <?php if(is_array($vo['child'])): if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?><tr class="pnode_<?php echo ($vo["id"]); ?> pnode_<?php echo ($v1["id"]); ?> cnode3_<?php echo ($vo["id"]); ?>" data-id="<?php echo ($v1["id"]); ?>" rid="<?php echo ($vo["id"]); ?>">
                    <td><span style="margin-left: <?php echo ($v1['level']*40+40); ?>px"><input type="checkbox" level="2" class="node_check check-level2" value="<?php echo ($v1["id"]); ?>" <?php if($v1['hv'] == 1): ?>checked<?php endif; ?> /><?php echo ($v1["title"]); ?></span></td>
                </tr>
                <?php if(is_array($v1['child'])): ?><tr class="pnode_<?php echo ($vo["id"]); ?> pnode_<?php echo ($v1["id"]); ?>" data-id="<?php echo ($v2["id"]); ?>" rid="<?php echo ($vo["id"]); ?>">
                        <td>
                            <?php if(is_array($v1["child"])): $i = 0; $__LIST__ = $v1["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($i % 2 );++$i; if($key == 0): ?><span style="margin-left: <?php echo ($v2['level']*40+40); ?>px"><input type="checkbox" level="3" class="node_check check-level3" <?php if($v2['hv'] == 1): ?>checked<?php endif; ?> /><?php echo ($v2["title"]); ?></span>
                                    <?php else: ?>
                                    <span style="margin-left: 20px"><input type="checkbox" level="3" class="node_check check-level3" value="<?php echo ($v2["id"]); ?>" <?php if($v2['hv'] == 1): ?>checked<?php endif; ?> /><?php echo ($v2["title"]); ?></span><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        </td>
                    </tr><?php endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
    <tr><td align="center"><a class="btn_sub save_btn" href="javascript:void(0);" style="margin-top: 20px;">保存</a></td></tr>
    <tr><td>&nbsp;</td></tr>
</table>

<script>
    $(function () {
        $('.node_table .node_check').click(function () {
            var this_tr = $(this).parents('tr');
            var this_id = $(this).val();
            var rid = this_tr.attr('rid');
            var level = $(this).attr('level');
            var isChecked = $(this).prop("checked");
            $('.pnode_'+this_id).find('.node_check').prop('checked', isChecked);
            if (isChecked) {
                this_tr.prevAll('.cnode'+level+'_'+rid).find('.node_check').prop('checked', isChecked);
            }
        });
        $('.select_all').click(function () {
            $('.node_table .node_check').prop('checked', $(this).prop("checked"));
        });
        $('.save_btn').click(function () {
            var roleid = $('#roleid').val();
            if (roleid == '') {
                layer.msg('参数错误', {icon:5,time:1000});
                return false;
            }
            var nodeid_str = '';
            $('.node_table .node_check').each(function () {
                if ($(this).prop("checked")) {
                    nodeid_str += (nodeid_str=='')?$(this).val():','+$(this).val();
                }
            });
            $.ajax({
                type:'post',
                dataType:'json',
                data:{roleid:roleid,nodeid_str:nodeid_str},
                url:'/Permission/saveAccess',
                error:function () {
                    layer.msg('未知错误', {icon:5,time:1000});
                },
                success:function (data) {
                    if (data.code == 1) {
                        layer.msg('保存成功', {icon:6,time:1000}, function () {
                            $('.layui-layer-close').trigger('click');
                        });
                    }else {
                        layer.msg(data.msg, {icon:5,time:1000});
                    }
                }
            });
        });
    });
</script>