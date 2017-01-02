@extends('layouts.master')

@section('content')
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

$('.popoverOption').each(function() {
    var $this = $(this);
    $this.popover({
      trigger: 'hover',
      placement: 'left',
      container: $this,
      html: true,
  
    });
});
});
</script>

     

<div class="row">	


</div>
@endsection
