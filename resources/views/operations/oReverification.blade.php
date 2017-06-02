                <div id="ot_reverify" style="display: block;">
                    <h3 class="m-t-sm">重新审核</h3>

                    <p>{{$reg->user->name}}{{$reg->type == 'dais' ? '申请成为本次会议学术团队成员' : ($reg->type == 'ot' ? '申请成为本次会议会务团队成员' : '报名参加本次会议')}}，请在“信息”页确认其报名信息。</p>
                    <p>此前，其并未通过审核。点击<strong>重新审核</strong>按钮后，其状态将还原至“等待组织团队审核”。</p>
                    <button name="reVerify" type="button" class="btn btn-danger" onclick="$('#ot_reverify').hide(); $('#ot_doreverify_confirm').show();">重新审核</button>

                </div>
                <div id="ot_doreverify_confirm" style="display: none;">
                    <h3 class="m-t-sm">危险！</h3>

                    <p>您确实要继续吗？</p>

                    <a class="btn btn-success" onclick="loader(this); jQuery.get('{{mp_url('/ot/oReVerify/'.$reg->id)}}', function(){$('#ajaxModal').load('{{mp_url('/ot/regInfo.modal/'.$reg->id.'?active=operations')}}');});">是</a>
                   <button name="cancel" type="button" class="btn btn-white" onclick="$('#ot_doreverify_confirm').hide(); $('#ot_reverify').show();">否</button>


                </div>
