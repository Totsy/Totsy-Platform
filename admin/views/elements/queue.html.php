<div class="box">
	<h2>
		<a href="#" id="toggle-queue">Currently in Queue</a>
	</h2>
	<div class="block" id="queue">
	    <table border="0" cellspacing="5" cellpadding="5">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Queue ID</th>
                    <th>Event Orders Queued</th>
                    <th>Event POs Queued</th>
                    <th>Expected Order/Line Count </th>
                    <th>Current Status</th>
                    <th>No of times the batch ran</th>
                </tr>
            </thead>
            <tbody id="queue_body">
                <tr>
                    <td colspan="8"><center>Loading...</center></td>
                </tr>
            </tbody>
        </table>
	</div>
</div>

<script type="text/javascript">
updateCurrentQueue();
setInterval("updateCurrentQueue()",30000);


function updateCurrentQueue() {
 /*   $('#queue_body').ajaxSend(function(){
        $(this).html("");
        $(this).append("<td colspan=\"5\"><center>Loading...</center></td>");
    }); */
    $.ajax({
        url:"/queue/currentQueue",
      //  global: true,
        type: "get",
        success: function(queue) {
            var block = $("#queue");
            queue = $.parseJSON(queue);
            body = $('#queue_body');
            if (queue != []) {
                body.html("");
                var index = 0;
                for( ; index < queue.length ; ++index) {
                    var table = "<tr>" +
                        "<td>" + (index +1) + "</td>" +
                        "<td>" + queue[index].created_date + "</td>" +
                        "<td>" + queue[index]._id + "</td>" +
                        "<td>" + queue[index].orders + "</td>" +
                        "<td>" + queue[index].purchase_orders + "</td>" +
                        "<td>" + queue[index].order_count + "/" +
                        queue[index].line_count + "</td>";
                    if (queue[index].status) {
                        table = table +  "<td>" + queue[index].status +
                            " (" + queue[index].percent + "%)" + "</td>";
                    } else{
                         table = table +  "<td></td>";
                    }
                    if (queue[index].run_amount) {
                        table = table + "<td>" + queue[index].run_amount + "</td>";
                    } else{
                         table = table +  "<td></td>";
                    }
                    table += "</tr>";

                    body.append(table);
                }
                if (index == 0 ) {
                    table += "<tr><td colspan=\"8\"><center>Nothing in the queue!</center></td></tr>";
                     body.append(table);
                }
            } else {
                table += "<tr><td colspan=\"8\"><center>Nothing in the queue!</center></td></tr>";
                body.append(table);
            }
        }
    });
}
</script>