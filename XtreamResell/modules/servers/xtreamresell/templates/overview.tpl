</head>
        <div id="wrapper">
            <section id="main">
                <header>
                    <img style="width: 225px;" src="" />
                </header>
          



<div class="panel panel-default">
   <div class="panel-heading" style="background-color: black; color: white;">
       <h3 class="panel-title">Login Username: {$moduleParams.username|htmlentities} Password: {$moduleParams.password|htmlentities}</h3>
   </div>
<hr>
<!DOCTYPE html>
<html>
<body>

<form>
  <b>Select your line type:</b>
  <select id="mySelect" style="background-color: black; color: white;>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=gigablue&output=ts">GigaBlue-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=enigma16&output=ts">Enigma.2.OE.1.6-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=dreambox&output=ts">DreamBox.OE.2.0-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=m3u_plus&output=ts">M3U-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=simple&output=ts">Simple.List-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=octagon&output=ts">Octagon-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=starlivev3&output=ts">StarLive.v3/StarSat.HD6060.AZClass-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=mediastar&output=ts">MediaStar/StarLive/Geant/Tiger-MPEGTS</option>
    <option value="wget -O /etc/enigma2/iptv.sh &quot;{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=enigma216_script&output=ts&quot; && chmod 777 /etc/enigma2/iptv.sh && /etc/enigma2/iptv.sh">Enigma.2.OE.1.6.AutoScript-MPEGTS</option>
    <option value="wget -O /etc/enigma2/iptv.sh &quot;{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=enigma22_script&output=ts&quot; && chmod 777 /etc/enigma2/iptv.sh && /etc/enigma2/iptv.sh&quot; && chmod 777 /etc/enigma2/iptv.sh && /etc/enigma2/iptv.sh">Enigma.2.OE.2.0.AutoScript-MPEGTS</option>
    <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=starlivev5&output=ts">StarLive.v5-MPEGTS</option>
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=webtvlist&output=ts">WebTVList-MPEGTS</option>  
    <option value="wget -qO /var/bin/iptv &quot;{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=octagon_script&output=ts&quot;">OctagonAutoscript-MPEGTS</option>   
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=ariva&output=ts">Ariva-MPEGTS</option>   
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=spark&output=ts">Spark-MPEGTS</option>    
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=gst&output=ts">Geant/Starsat/Tiger/Qmax/Hyper/Royal(old)-MPEGTS</option>
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=fps&output=ts">Fortec99/Prifix9400/Starport-MPEGTS</option>    
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=revosun&output=ts">Revolution6060|Sunplus-MPEGTS</option>   
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=starsat7000&output=ts">Starsat.7000-MPEGTS</option>   
	 <option value="{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=zorro&output=ts">Zorro-MPEGTS</option>   
  </select>
</form>

<p>Click the button to return the value.</p>

<button style="background-color: black; color: white; type="button" onclick="myFunction()">Get</button>

<p id="demo"></p>

<script>
function myFunction() {
  var x = document.getElementById("mySelect").value;
  document.getElementById("demo").innerHTML = x;
}
</script>
</body>
</html>
<img src="{$logo}" alt="">
 <hr>
<table style="width:100%">
  <tr>
    <th>Login</th>
    <th></th> 
    
  </tr>
  <tr>
    <td>Username</td>
    <td>{$moduleParams.username|htmlentities}</td> 
    
  </tr>
  <tr>
    <td>Password</td>
    <td>{$moduleParams.password|htmlentities}</td> 
 
  </tr><tr>
    <td>MAG Portal</td>
    <td>{$moduleParams.configoption4|htmlentities}c</td> 
 
  </tr>
  </tr><tr>
    <td>Web Portal</td>
    <td>{$moduleParams.configoption4|htmlentities}client_area</td> 
  </tr>

  
</table>
<a href="{$moduleParams.configoption4|htmlentities}client_area" target="_blank">
<input type="button" class="button" value="Open WebPortal" />
</a>
<a href="https://www.subportal.io/generator/{$moduleParams.configoption18|htmlentities}" target="_blank">
<input type="button" class="button" value="OPEN M3U/SMARTTV/URL SHORTNER" />
</a>
<a href="http://www.subportal.io/channels/{$moduleParams.configoption18|htmlentities}" target="_blank">
<input type="button" class="button" value="OPEN WEBPLAYER" />
</a>
<a href="http://www.subportal.io/channellist/{$moduleParams.configoption18|htmlentities}" target="_blank">
<input type="button" class="button" value="OPEN CHANNEL LIST" />
</a>
<hr>
<img src="http://iptvextreme.eu/banner.png" alt="">
<hr>
<a href="http://iptvextreme.eu/" target="_blank">
<input type="button" class="button" value="Open IPTV Extreme Portal" />
</a>
 <hr>
</tr><tr>
    <td>IPTV EXTREME M3U Playlist:</td>
    <td><br/>{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=m3u_plus&output=ts</td> 
  </tr>
   <hr>
  </tr>
  </tr><tr>
    <td>IPTV EXTREME CATCHUP M3U Playlist:</td>
    <td><br/>{$moduleParams.configoption4|htmlentities}get.php?username={$moduleParams.username|htmlentities}&password={$moduleParams.password|htmlentities}&type=m3u_plus&output=ts|catchup=xc</td> 
  </tr>
 <hr>
</tr>
  

<div class="alert alert-info">
   
</div>
<h3>{$LANG.clientareaproductdetails}</h3>

<hr>

<div class="row">
    <div class="col-sm-5">
        {$LANG.clientareahostingregdate}
    </div>
    <div class="col-sm-7">
        {$regdate}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderproduct}
    </div>
    <div class="col-sm-7">
        {$groupname} - {$product}
    </div>
</div>

{if $type eq "server"}
    {if $domain}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.serverhostname}
            </div>
            <div class="col-sm-7">
                {$domain}
            </div>
        </div>
    {/if}
    {if $dedicatedip}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.primaryIP}
            </div>
            <div class="col-sm-7">
                {$dedicatedip}
            </div>
        </div>
    {/if}
    {if $assignedips}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.assignedIPs}
            </div>
            <div class="col-sm-7">
                {$assignedips|nl2br}
            </div>
        </div>
    {/if}
    {if $ns1 || $ns2}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.domainnameservers}
            </div>
            <div class="col-sm-7">
                {$ns1}<br />{$ns2}
            </div>
        </div>
    {/if}
{else}
    {if $domain}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.orderdomain}
            </div>
            <div class="col-sm-7">
                {$domain}
                <a href="http://{$domain}" target="_blank" class="btn btn-default btn-xs">{$LANG.visitwebsite}</a>
            </div>
        </div>
    {/if}
    {if $username}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.serverusername}
            </div>
            <div class="col-sm-7">
                {$username}
            </div>
        </div>
    {/if}
    {if $serverdata}
        <div class="row">
            <div class="col-sm-5">
                {$LANG.servername}
            </div>
            <div class="col-sm-7">
                {$serverdata.hostname}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                {$LANG.domainregisternsip}
            </div>
            <div class="col-sm-7">
                {$serverdata.ipaddress}
            </div>
        </div>
        {if $serverdata.nameserver1 || $serverdata.nameserver2 || $serverdata.nameserver3 || $serverdata.nameserver4 || $serverdata.nameserver5}
            <div class="row">
                <div class="col-sm-5">
                    {$LANG.domainnameservers}
                </div>
                <div class="col-sm-7">
                    {if $serverdata.nameserver1}{$serverdata.nameserver1} ({$serverdata.nameserver1ip})<br />{/if}
                    {if $serverdata.nameserver2}{$serverdata.nameserver2} ({$serverdata.nameserver2ip})<br />{/if}
                    {if $serverdata.nameserver3}{$serverdata.nameserver3} ({$serverdata.nameserver3ip})<br />{/if}
                    {if $serverdata.nameserver4}{$serverdata.nameserver4} ({$serverdata.nameserver4ip})<br />{/if}
                    {if $serverdata.nameserver5}{$serverdata.nameserver5} ({$serverdata.nameserver5ip})<br />{/if}
                </div>
            </div>
        {/if}
    {/if}
{/if}

{if $dedicatedip}
    <div class="row">
        <div class="col-sm-5">
            {$LANG.domainregisternsip}
        </div>
        <div class="col-sm-7">
            {$dedicatedip}
        </div>
    </div>
{/if}

{foreach from=$configurableoptions item=configoption}
    <div class="row">
        <div class="col-sm-5">
            {$configoption.optionname}
        </div>
        <div class="col-sm-7">
            {if $configoption.optiontype eq 3}
                {if $configoption.selectedqty}
                    {$LANG.yes}
                {else}
                    {$LANG.no}
                {/if}
            {elseif $configoption.optiontype eq 4}
                {$configoption.selectedqty} x {$configoption.selectedoption}
            {else}
                {$configoption.selectedoption}
            {/if}
        </div>
    </div>
{/foreach}

{foreach from=$productcustomfields item=customfield}
    <div class="row">
        <div class="col-sm-5">
            {$customfield.name}
        </div>
        <div class="col-sm-7">
            {$customfield.value}
        </div>
    </div>
{/foreach}

{if $lastupdate}
    <div class="row">
        <div class="col-sm-5">
            {$LANG.clientareadiskusage}
        </div>
        <div class="col-sm-7">
            {$diskusage}MB / {$disklimit}MB ({$diskpercent})
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5">
            {$LANG.clientareabwusage}
        </div>
        <div class="col-sm-7">
            {$bwusage}MB / {$bwlimit}MB ({$bwpercent})
        </div>
    </div>
{/if}

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderpaymentmethod}
    </div>
    <div class="col-sm-7">
        {$paymentmethod}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.firstpaymentamount}
    </div>
    <div class="col-sm-7">
        {$firstpaymentamount}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.recurringamount}
    </div>
    <div class="col-sm-7">
        {$recurringamount}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.clientareahostingnextduedate}
    </div>
    <div class="col-sm-7">
        {$nextduedate}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.orderbillingcycle}
    </div>
    <div class="col-sm-7">
        {$billingcycle}
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        {$LANG.clientareastatus}
    </div>
    <div class="col-sm-7">
        {$status}
    </div>
</div>

{if $suspendreason}
    <div class="row">
        <div class="col-sm-5">
            {$LANG.suspendreason}
        </div>
        <div class="col-sm-7">
            {$suspendreason}
        </div>
    </div>
{/if}

<hr>

<div class="row">
    <div class="col-sm-4">
        <form method="post" action="clientarea.php?action=productdetails">
            <input type="hidden" name="id" value="{$serviceid}" />
            <input type="hidden" name="customAction" value="manage" />
            <button type="submit" class="btn btn-default btn-block">
                Custom Client Area Page
            </button>
        </form>
    </div>

    {if $packagesupgrade}
        <div class="col-sm-4">
            <a href="upgrade.php?type=package&amp;id={$id}" class="btn btn-success btn-block">
                {$LANG.upgrade}
            </a>
        </div>
    {/if}

    <div class="col-sm-4">
        <a href="clientarea.php?action=cancel&amp;id={$id}" class="btn btn-danger btn-block{if $pendingcancellation}disabled{/if}">
            {if $pendingcancellation}
                {$LANG.cancellationrequested}
            {else}
                {$LANG.cancel}
            {/if}
        </a>
    </div>
</div>
