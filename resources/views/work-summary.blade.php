<style>
*{
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}
body{
    font-family: Helvetica;
    -webkit-font-smoothing: antialiased;
    background: white;
}
h2{  
    text-align: center;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: black;
    /* padding: 30px 0; */
}
/* Table Styles */
.table-wrapper{
    margin: 0px 7px 7px;
    box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 );
}

.fl-table {
    /* border-radius: 5px; */
    font-size: 12px;
    font-weight: normal;
    border: none;
    /* border-collapse: collapse; */
    width: 100%;
    max-width: 100%;
    white-space: nowrap;
    background-color: white;
}

.fl-table td, .fl-table th {
    /* text-align: center; */
    /* padding: 8px; */
}

.fl-table td {
    border-right: 1px solid #f8f8f8;
    font-size: 12px;
}

.fl-table thead th {
    width: 50%;
    color: black;
    background: white;
}


.fl-table thead th:nth-child(odd) {
    color: black;
    background: white;
}

.fl-table tr:nth-child(even) {
    background: #F8F8F8;
}
.fl-td{
  float:right;
  font-weight: bold; 
}
table {width:100%;}
table td {border:solid 1px #605d5d;  word-wrap:break-word;}
td {
width:30%;
/* css-3 */
white-space: -o-pre-wrap; 
word-wrap: break-word;
white-space: pre-wrap; 
white-space: -moz-pre-wrap; 
white-space: -pre-wrap; 
}
.red{
    color:red;
}
tr.no-bottom-border td {
  border-top: none;
  border-bottom: none;
}
.page_break { page-break-before: always; }
  </style>
<div class="table-wrapper">
  <img align="right" src="https://www.kindpng.com/picc/m/167-1675572_transparent-construction-hat-clipart-engineer-hat-icon-png.png" width="auto" height="50" style="position: relative;"> 
</div> 
<div class="table-wrapper">
<h2 style="">ELEMENTAL SUMMARY</h2>
</div>






<div class="table-wrapper">


<table class="fl-table" style="margin-bottom: 5%;">
        <tbody>
        <tr>
            <td align="center" style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;" >Item</td>
            <td align="center" class="fl-td" width="300" style="color:red;width:200px;border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Level one summary - Elements</td>
            <td align="center" class="fl-td"  style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Workdone</td> 
            <td align="center" class="fl-td"  style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Materials</td> 
            <td align="center" class="fl-td" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Labour</td> 
            <td align="center" class="fl-td" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Equipment</td> 
            <td align="center" class="fl-td" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Profit/Loss</td>  
        </tr>
        <tr>   

            <td align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td>
            <td class="fl-td" align="left" style="color:red; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Elements</td>
            <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
            <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
            <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
            <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
            <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td>  
        </tr>
        @foreach ($elementarysummary as $elementarysummation)
        <tr class="no-bottom-border">
            <td align="center"  style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[0]}}</td>
            <td class="fl-td" align="left" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[1]}}</td>
            <td class="fl-td" align="right" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[2]}}</td> 
            <td class="fl-td" align="right" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[3]}}</td> 
            <td class="fl-td" align="right" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[4]}}</td> 
            <td class="fl-td" align="right" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[5]}}</td> 
            <td class="fl-td" align="right" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[6]}}</td>  
        </tr>
        @endforeach  
        <tbody>
    </table>
    </div>


<div class="page_break"></div>
<div class="table-wrapper">
  <img align="right" src="https://www.kindpng.com/picc/m/167-1675572_transparent-construction-hat-clipart-engineer-hat-icon-png.png" width="auto" height="50" style="position: relative;"> 
</div> 
<div class="table-wrapper">
<h2 style="">ELEMENTAL ITEM BREAKDOWN - SUMMARY</h2>
</div>



<div class="table-wrapper">
        @foreach ($elementarysummary as $key =>$elementarysummation)
        <table class="fl-table" style="margin-bottom: 2%;width:100%;table-layout: auto;">
            <tbody>
                @if ($key == 0)
                <tr>
                    <td align="center" style="width:40px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;" >Item</td>
                    <td align="center" class="fl-td" style="color:red;width:200px;border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Level two summary - Elementally</td>
                    <td align="center" class="fl-td"  style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Unit</td> 
                    <td align="center" class="fl-td"  style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Quantity</td> 
                    <td align="center" class="fl-td" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Rate</td> 
                    <td align="center" class="fl-td" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Amount</td>                    
                </tr>
                @endif
                <tr> 
                    <td align="center" style="width:40px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td>
                    <td class="fl-td" align="left" style="color:red;width:200px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">Element no {{ $elementarysummation[0]}}</td>
                    <td class="fl-td" align="center" style=" width:80px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" width:80px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" width:80px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" width:80px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                   
                </tr>
                <tr>
                    <td align="center"  style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td>
                    <td class="fl-td" align="left" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{{ $elementarysummation[1]}}</td>
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                  
                </tr>
            <?php
                 //var_dump($elements[$elementarysummation[1]]); 
                // exit();            
            ?>
             <?php
             if(isset($elements[$elementarysummation[1]])){
             ?>
              <!--loop rows --> 
              @foreach ($elements[$elementarysummation[1]] as $key =>$elementlevel2)                  
               <tr class="no-bottom-border">
                 <td align="center"  style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! $elementlevel2[0] !!}</td>
                    <td class="fl-td" align="left" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! $elementlevel2[1] !!}</td>
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td> 
                    <td class="fl-td" align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td>                 
                </tr>
                @foreach ($elementlevel2[2] as $key =>$elementlevel3)
                <tr class="no-bottom-border">
                 <td align="center"  style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;"></td>
                    <td  align="left" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! preg_replace('/\d/', '',str_replace('.','',$elementlevel3->title)) !!}</td>
                    <td  align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! $elementlevel3->unit !!}</td> 
                    <td  align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! $elementlevel3->quantity !!}</td> 
                    <td  align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! $elementlevel3->rate !!}</td> 
                    <td  align="center" style=" border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;">{!! $elementlevel3->amount !!}</td>                   
                </tr>
             
                @endforeach 
              @endforeach 
              <?php 
              }
              ?>
            <tbody>
        </table>
        @endforeach 
        
        

<!--
    <div class="table-wrapper">
    <table class="fl-table">
        <tbody>
        <tr>
            <td class="fl-td" align="center" style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;" >Item</td>
            <td class="fl-td" style="color:red;">Workdone Summary</td>
            <td class="fl-td" align="center">Unit</td>
            <td class="fl-td" align="center">Quantity</td>
            <td class="fl-td" align="center">Rate</td>
            <td class="fl-td" align="center">Amount</td>
        </tr>
        @foreach ($workdonesummary as $workdonesummation)
        <tr class="no-bottom-border">
            <td></td>
            <td style="word-wrap: break-word;">{{ $workdonesummation[0]}}</td>
            <td  align="center">{{ $workdonesummation[1]}}</td>
            <td  align="center">{{ $workdonesummation[2]}}</td>
            <td  align="right">{{ $workdonesummation[3]}}</td>           
            <td  align="right">{{ $workdonesummation[4]}}</td>
        </tr>
        @endforeach

        <tbody>
    </table>
</div>-->


<div class="table-wrapper">
    <table class="fl-table">
        <tbody>
        <tr class="no-bottom-border">
            <td class="fl-td" align="center" style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;" ></td>
            <td class="fl-td" style="color:red;" >Material Summary</td>
            <td class="fl-td" align="center" ></td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
        </tr>
        @foreach ($materialsummary as $materialsummation)
        <tr>
            <td></td>
            <td style="word-wrap: break-word;">{{ $materialsummation[0]}}</td>
            <td align="center">{{ $materialsummation[1]}}</td>
            <td align="center">{{ $materialsummation[2]}}</td>
            <td align="right">{{ $materialsummation[3]}}</td>   
            <td class="fl-td" align="right">{{ $materialsummation[4]}}</td>
        </tr>
        @endforeach
        <tbody>
    </table>
</div>

<div class="table-wrapper">
    <table class="fl-table">
        <tbody>
        <tr>
            <td class="fl-td" align="center" style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;" ></td>
            <td class="fl-td" style="color:red;">Labour Summary</td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
        </tr>
        @foreach ($laboursummary as $laboursummation)
        <tr class="no-bottom-border">
            <td></td>
            <td>{{ $laboursummation[0]}}</td>
            <td align="center" style="word-wrap: break-word;">{{ $laboursummation[1]}}</td>
            <td align="center">{{ $laboursummation[2]}}</td>
            <td align="right">{{ $laboursummation[3]}}</td> 
            <td class="fl-td" align="right">{{ $laboursummation[4]}}</td>
        </tr>
        @endforeach
        <tbody>
    </table>
</div>

<div class="table-wrapper">
    <table class="fl-table">
        <tbody>
        <tr>
            <td class="fl-td" align="center" style="width:50px; border-left: 1px solid #605d5d;border-right: 1px solid #605d5d;" ></td>
            <td class="fl-td" style="color:red;">Equipment Summary</td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
            <td class="fl-td" align="center"></td>
        </tr>
        @foreach ($equipmentsummary as $equipmentsummation)
        <tr class="no-bottom-border">
            <td></td>
            <td>{{ $equipmentsummation[0]}}</td>
            <td align="center">{{ $equipmentsummation[1]}}</td>
            <td align="center">{{ $equipmentsummation[2]}}</td>
            <td align="right">{{ $equipmentsummation[3]}}</td> 
            <td class="fl-td" align="right">{{ $equipmentsummation[4]}}</td>
        </tr>
        @endforeach

        <tbody>
    </table>
</div>        



<!--
<div class="page_break"></div>
<div class="table-wrapper">
  <img align="right" src="https://www.kindpng.com/picc/m/167-1675572_transparent-construction-hat-clipart-engineer-hat-icon-png.png" width="auto" height="50" style="position: relative;"> 
</div> 
<div class="table-wrapper">
<h2 style="">TOTALS - SUMMARY</h2>
</div>
<div class="table-wrapper">
<table class="fl-table" style="margin-bottom: 5%;">
        <tbody>
        <tr>
            <td>Work Done</td>
            <td class="fl-td" align="right">{{ $workdone }}</td> 
        </tr>
        <tr>
            <td>Materials</td>
            <td class="fl-td" align="right">{{ $materials }}</td> 
        </tr>
        <tr>
            <td>Labour</td>
            <td class="fl-td" align="right">{{ $labour }}</td> 
        </tr>
        <tr>
            <td>Equipment</td>
            <td class="fl-td" align="right">{{ $equipment }}</td> 
        </tr>
        <tr>
            <td>Profit/loss</td>
            <td class="fl-td" align="right">{{ $profitloss }}</td> 
        </tr>
        <tbody>
    </table>
    </div>-->
 