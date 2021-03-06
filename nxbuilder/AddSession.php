<?php
/**************************************************************************/
/*                                                                        */
/* Copyright (c) 2005, 2011 NoMachine, http://www.nomachine.com.           */
/*                                                                        */
/* NXBUILDER, NX protocol compression and NX extensions to this software  */
/* are copyright of NoMachine. Redistribution and use of the present      */
/* software is allowed according to terms specified in the file LICENSE   */
/* which comes in the source distribution.                                */
/*                                                                        */
/* Check http://www.nomachine.com/licensing.html for applicability.       */
/*                                                                        */
/* NX and NoMachine are trademarks of Medialogic S.p.A.                   */
/*                                                                        */
/* All rigths reserved.                                                   */
/*                                                                        */
/**************************************************************************/
header("Cache-Control: no-store, no-cache, must-revalidate");  
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          




include ("HandleDB.php");
include ("conf.php");
include ("Messages.php");

checkPermission(); 

$upResult=false;
$error=false;
$messageType="";
$check_grabKeyboard="";
$check_directDraw="";
$dis_deskType="";
$lastSessionID=""; 
$check_CustomDisplaySettings="";
$check_NoZlibStreamCompression="";
$check_HttpProxy="";
$check_lazyEncoder="";
$check_rememberProxyAuth="";
$check_EnableMultimedia="";
$check_useFontServer="";
$XdmPortBroadCast="";
$fontServerHostValue="";
$fontServerPortValue="";
$check_RdpCredential2="";
if(!isset($_POST['WindowDomainValue'])) $WindowDomainValue="";

if(isset($_POST['uploadFile_x']) && (trim($_POST['nameCategory'])!=""))
{

    $directory = 'images/sessions/';
    $path1 = $_FILES['file1']['tmp_name'];
    $name = $_FILES['file1']['name'];
    if(isset($_POST['nameCategory']))
    {
     $nameNoExt = explode('.', $name);
     $nameCategory = strtolower($_POST['nameCategory']);
     $nameExt = ''.$nameCategory.'.'.$nameNoExt[1];
    }

    if (!empty($path1))
    {
      if(($result=preg_match('/\\.(gif|png|jpg|jpeg|bmp|img)$/i', $_FILES['file1']['name'])) && (move_uploaded_file($path1, $directory.$nameExt)))
      {
       $messageType = "UploadedFile";
       $imgSessionValue=$nameExt; 
       $upResult=true;
      }  
      else
      {
        $messageType = "UploadedFileError";
        $error = true;
        $upResult=false;
      }  
    }
}

if(isset($_POST['uploadFile_x']) && ((trim($_POST['nameCategory'])=="") ))
{
 $error = true;
 $messageType = "UploadEmptyField";
}

if( isset($_POST['addSession_x']) && (!empty($_POST['serverId'])) )
{ 

 if(isset($_POST['image']))
 {
  $_SESSION['path']=$_POST['image'];      
 }
 
 $_SESSION['from']='addServer';

 $error= false;
 $messageType="";
   
 if (trim($_POST['sessionName']) == "")
 { 
   
   $error= true;
   $messageType="EmptySessionName";
 }   
 elseif($_POST['sessionType']=="unix")
 {
   switch ($_POST['deskType'])
   {
    case('xdm'): 

     if($_POST['xdmMode']=="query")
     {
      if(trim($_POST['XdmDomainQuery']==""))
      {
        $error= true;
        $messageType="XDMEmptyDomain";       
      }           
      elseif(trim($_POST['XdmPortQuery']==""))
      {
        $error= true;
        $messageType="XDMEmptyPort";       
      }
      elseif(!is_numeric($_POST['XdmPortQuery']))
      {
        $error= true;
        $messageType="XDMPortFormat";       
      }
     }
     elseif($_POST['xdmMode']=="broadcast")
     {
      if(trim($_POST['XdmPortBroadCast']==""))
      {
        $error= true;
        $messageType="XDMEmptyPort";       
      }
      elseif(!is_numeric($_POST['XdmPortBroadCast']))
      {
        $error= true;
        $messageType="XDMPortFormat";       
      }
     } 
     elseif($_POST['xdmMode']=="list")
     {
      if(trim($_POST['XdmDomainList']==""))
      {
        $error= true;
        $messageType="XDMEmptyDomain";       
      }           
      elseif(trim($_POST['XdmPortList']==""))
      {
        $error= true;
        $messageType="XDMEmptyPort";       
      }
      elseif(!is_numeric($_POST['XdmPortList']))
      {
        $error= true;
        $messageType="XDMPortFormat";       
      }
     }
     break;
    
    case('custom'): 
    

     if($_POST['runApplication']=="application")
     {
      if(trim($_POST['customCommand']==""))
      {
        $error= true;
        $messageType="CustomEmptyCommand";       
      }      
     }
    break;
    
   }
 }
 elseif($_POST['sessionType']=="windows")
 {

  if($_POST['RdpCredential']=="1")
  {
    if(trim($_POST['WindowServer']==""))  
    {
      $error= true;
      $messageType="WindowsEmptyServer";     
    }
   
  } 
  elseif($_POST['RdpCredential']=="0")
  {
    if(trim($_POST['WindowServer']==""))  
    {
      $error= true;
      $messageType="WindowsEmptyServer";     
    }
    elseif(trim($_POST['WindowDomain']==""))  
    {
      //$error= true;
      //$messageType="WindowsEmptyDomain";     
    }
    elseif(trim($_POST['usernameWindows']==""))  
    {
      $error= true;
      $messageType="WindowsEmptyUserName";     
    }
    elseif(trim($_POST['passwordWindows']==""))  
    {
      $error= true;
      $messageType="WindowsEmptyPassword";     
    }
  }
  elseif($_POST['RdpCredential']=="2")
  {
    if(trim($_POST['WindowServer']==""))  
    {
      $error= true;
      $messageType="WindowsEmptyServer";     
    }
    elseif(trim($_POST['WindowDomain']==""))  
    {
      //$error= true;
      //$messageType="WindowsEmptyDomain";     
    }
  }
 }              
 elseif($_POST['sessionType']=="vnc")
 {
    if(trim($_POST['VncHost']==""))  
    {
      $error= true;
      $messageType="VncEmptyServer";     
    }
    elseif(trim($_POST['VncPort']==""))  
    {
      $error= true;
      $messageType="VncEmptyPort";     
    }
    elseif(!is_numeric($_POST['VncPort']))  
    {
      $error= true;
      $messageType="VncPortFormat";     
    }
    elseif(trim($_POST['vncPassword']==""))  
    {
      $error= true;
      $messageType="VncEmptyPassword";     
    }
 } 

 if(isset($_POST['HttpProxy']))            
 {

  if(trim($_POST['proxyHost']==""))  
  {
    $error= true;
    $messageType="ProxyEmptyServer";      
  }
  elseif(trim($_POST['proxyPort']==""))  
  {
    $error= true;
    $messageType="ProxyEmptyPort";     
  }   
  elseif(!is_numeric($_POST['proxyPort']))  
  {
    $error= true;
    $messageType="ProxyPortFormat";     
  }
  elseif(trim($_POST['userNameProxy']==""))  
  {
    $error= true;
    $messageType="ProxyEmptyUserName";     
  }  
  elseif(trim($_POST['passwordProxy']==""))  
  {
    $error= true;
    $messageType="ProxyEmptyPassword";     
  }
 }


 if(isset($_POST['useFontServer']))            
 { 
  if(trim($_POST['fontServerHost']==""))  
  {
    $error= true;
    $messageType="FontEmptyServer";      
  }
  elseif(trim($_POST['fontServerPort']==""))  
  {
    $error= true;
    $messageType="FontEmptyPort";     
  }   
  elseif(!is_numeric($_POST['fontServerPort']))  
  {
    $error= true;
    $messageType="FontPortFormat";      
  }
} 

 if(!$error)
 { 
    if(!isset($_POST['CustomDisplaySettings'])) $cusDis=0; //$_POST['CustomDisplaySettings']=0;
    else $cusDis=$_POST['CustomDisplaySettings'];
    
    if(!isset($_POST['EnableSSL'])) $enSsl="true"; // $_POST['EnableSSL']="true";
    else $enSsl=$_POST['EnableSSL'];
    
    if(!isset($_POST['NoDelayTcp'])) $nodelay="false"; // $_POST['NoDelayTcp']="false";
    else $nodelay=$_POST['NoDelayTcp'];
    
    if(!isset($_POST['grabKeyboard'])) $grab="false"; // $_POST['grabKeyboard']="false";
    else $grab=$_POST['grabKeyboard'];

    if(!isset($_POST['directDraw'])) $directDraw="false"; // $_POST['directDraw']="false";
    else $directDraw=$_POST['directDraw'];
    
    if(!isset($_POST['lazyEncoder'])) $lazy="false"; // $_POST['lazyEncoder']="false";
    else $lazy=$_POST['lazyEncoder'];
     
    if(!isset($_POST['NoZlibStreamCompression'])) $nozlib="false"; //$_POST['NoZlibStreamCompression']="false";
    else $nozlib=$_POST['NoZlibStreamCompression'];
   
    
    if(!isset($_POST['HttpProxy'])) $htproxy="false"; //$_POST['HttpProxy']="false";
    else $htproxy=$_POST['HttpProxy'];
    
    if(!isset($_POST['EnableMultimedia'])) $enMulti="false"; // $_POST['EnableMultimedia']="false";
    else $enMulti=$_POST['EnableMultimedia'];
    
    if(!isset($_POST['useFontServer'])) $useFont="false"; // $_POST['useFontServer']="false";
    else $useFont=$_POST['useFontServer'];
    
    if(!isset($_POST['deskType'])) $_POST['deskType']=$_POST['sessionType'];    

    //$lastSessionID=InsertSession($_POST['sessionName'],$_POST['sessionType'],$_POST['deskType'],$_POST['CustomDisplaySettings'], $_POST['NoDelayTcp'],$_POST['NoZlibStreamCompression'] ,$_POST['EnableSSL'], $_POST['lazyEncoder'], $_POST['HttpProxy'], $_POST['EnableMultimedia'], $_POST['useFontServer'],$_POST['icon'],$_POST['serverId']);
    
    $lastSessionID=InsertSession($_POST['sessionName'],$_POST['sessionType'],$_POST['deskType'],$cusDis, $nodelay,$nozlib ,$enSsl, $grab, $directDraw,$lazy, $htproxy, $enMulti, $useFont,$_POST['icon'],$_POST['serverId']); 
    
    if($lastSessionID==-1)
    {
      $error= true;
      $messageType="ErrorSessionNameExist";            
    }
    elseif($lastSessionID==0)
    {
      $error= true;
      $messageType="ErrorSessionUnknown";      
    } 
    else
    { 
     $error= false;
     if($_POST['sessionType']=="unix")
     {
       switch ($_POST['deskType'])
       {
        case('xdm'): 

         if(!isset($_POST['XdmPortList'])) $portXdmList="177";
         else $portXdmList=$_POST['XdmPortList'];
         
         if(!isset($_POST['XdmDomainList']))  $domainXdmList="localhost";
         else $domainXdmList=$_POST['XdmDomainList'];
         
         if(!isset($_POST['XdmPortQuery'])) $portXdmQuery="177";
         else $portXdmQuery=$_POST['XdmPortQuery'];
                    
         if(!isset($_POST['XdmDomainQuery']))  $domainXdmQuery="localhost";
         else $domainXdmQuery=$_POST['XdmDomainQuery'];
         
         if(!isset($_POST['XdmPortBroadCast']))  $portXdmBroadcast="177";
         else $portXdmBroadcast=$_POST['XdmPortBroadCast'];
                   
         if(!InsertXdmOption($lastSessionID, $_POST['xdmMode'], $portXdmList, $domainXdmList, $portXdmQuery, $domainXdmQuery, $portXdmBroadcast))
         {
          $error= true;
          $messageType="XDMInsertError"; 
         }
        break;
        
        case('custom'): 
        

         if(!isset($_POST['customCommand']))  $cusCom="";
         else $cusCom=$_POST['customCommand'];
         
         if(!isset($_POST['disableX']))  $disX="true";
         else $disX=$_POST['disableX'];
         
         if(!isset($_POST['taint']))  $tai="true";
         else $tai=$_POST['taint'];
         
                  
         if(!InsertCustomOption($lastSessionID, $_POST['runApplication'],$cusCom,$_POST['customOptions'],$disX, $tai))
         {
          $error= true;
          $messageType="CustomInsertError"; 
         }
        break;
        
       }
     }
     elseif($_POST['sessionType']=="windows")
     {

        if(!isset($_POST['WindowServer'])) $winSer="";
        else $winSer=$_POST['WindowServer'];
        
        if(!isset($_POST['WindowDomain']))  $winDom="";
        else $winDom=$_POST['WindowDomain'];
        
        if(!isset($_POST['usernameWindows'])) $winUser="";
        else $winUser=$_POST['usernameWindows'];
        
        if(!isset($_POST['passwordWindows'])) $winPass="";
        else $winPass=$_POST['passwordWindows'];
        
        if(!isset($_POST['RememberPasswordWindows'])) $winRem="false";
        else $winRem=$_POST['RememberPasswordWindows'];
        
        if(!isset($_POST['runWindowsType'])) $winAppType="false";
        else $winAppType=$_POST['runWindowsType'];
        
        if(!isset($_POST['applicationWindows'])) $winApp="";
        else $winApp=$_POST['applicationWindows'];        

        if(!InsertRdpOption($lastSessionID, $winSer, $winDom, $_POST['RdpCredential'], $winUser, $winPass, $winRem, $winAppType, addslashes($winApp) ))
        {
          $error= true;
          $messageType="RDPInsertError";         
        }
     }              
     elseif($_POST['sessionType']=="vnc")
     {
        if(!isset($_POST['VncHost']))  $vncH="";
        else $vncH=$_POST['VncHost'];
        
        if(!isset($_POST['VncPort']))  $portVnc="";
        else $portVnc=$_POST['VncPort'];
        
        if(!isset($_POST['vncPassword'])) $passVnc="";
        else $passVnc=$_POST['vncPassword'];
        
        if(!isset($_POST['vncRememberPassword'])) $passRem="false";
        else $passRem=$_POST['vncRememberPassword'];
     
        if(!InsertVncOption($lastSessionID, $vncH, $portVnc, $passVnc, $passRem))
        {
          $error= true;
          $messageType="VNCInsertError";                  
        }

     } 
     
     
     
     if($cusDis!=0) //$_POST['CustomDisplaySettings']
     {
     
   	  
       //unix display 
       
       
       
       if($_POST['enableJpegQuality']=='-1') 
       {
        if($_POST['compressionType']=="3")
        {
         $_POST['compressionType']="4";
        }
        elseif($_POST['compressionType']=="1")
        {
         $_POST['compressionType']="-1";
        }
       }
    
       
       if(!isset($_POST['renderExt']))  $rExt="true";
       else $rExt=$_POST['renderExt'];

       if(!isset($_POST['backingStore']))  $bStore="false";
       else $bStore=$_POST['backingStore'];
       
       if(!isset($_POST['composite']))  $cSite="false";
       else $cSite=$_POST['composite'];
       
       if(!isset($_POST['shm']))  $sharedMem="false";
       else $sharedMem=$_POST['shm'];
       
       if(!isset($_POST['sharedPix']))  $sPix="false";
       else $sPix=$_POST['sharedPix'];
 
       
       //rdp display
       if(isset($_POST['winCompMaps']))  $_POST['winComp']="1";

       if(!isset($_POST['rdpCache']))  $rCache="false";
       else $rCache=$_POST['rdpCache'];
       

       if(isset($_POST['useRdpJpg'])) 
       {
        if($_POST['winComp']=="3")
        {
         $_POST['winComp']="4";
        }
        elseif($_POST['winComp']=="1")
        {
         $_POST['winComp']="-1";
        }
       }
       
       //vnc display
       
       
       

       if(isset($_POST['useVncJpg'])) 
       {
        if($_POST['encodType']=="3")
        {
         $_POST['encodType']="4";
        }
        elseif($_POST['encodType']=="1")
        {
         $_POST['encodType']="-1";
        }
       }       

        if(!InsertDisplayOption($lastSessionID, $rExt, $bStore, $cSite,  $_POST['compressionType'], $_POST['encodType'],  $_POST['jpegQuality'],  $_POST['winComp'], $sharedMem, $sPix, $_POST['rdpQuality'], $rCache,$_POST['jpegQualityRdp'],$_POST['jpegQualityVnc']))
        {
          $error= true;
          $messageType="DisplayInsertError";          
        }
       
    } //end if($_POST['CustomDisplaySettings']!=0)
    
     if($_POST['HttpProxy']=='true')            
     {
      if(!insertHttpProxy($lastSessionID, $_POST['proxyHost'], $_POST['proxyPort'], $_POST['userNameProxy'], $_POST['passwordProxy'], $_POST['rememberProxyAuth']))
      {
        $error= true;
        $messageType="ProxyInsertError";  
      }
     }
     
     if($_POST['useFontServer']=='true')            
     {
      if(!insertFontServer($lastSessionID, $_POST['fontServerHost'], $_POST['fontServerPort']))
      {
        $error= true;
        $messageType="FontInsertError"; 
      }
     }      
     
      $_SESSION["status"]= "AddedSession";
      unset($_SESSION['path']);
      
      if(!$error)
      { 
       $messageType="AddedSession";
       header('Location: SessionList.php');
      }  
    }//end if ($LaseSessId)
  } //end if !error
}
elseif( isset($_POST['addSession_x']) && (empty($_POST['serverId'])) )
{
  $messageType="NoServerSelected";
}


if($error) //set default fields values with FORM values
{

 undoInsertSession($lastSessionID);


 $selUnix="";
 $selWin="";
 $selVnc="";
 $selShadow="";
 
 

 if($_POST['sessionType']=="unix")
 {
  $selUnix=" selected";
  
  $selkde="";
  $selgnome="";
  $selcde="";
  $selxdm="";
  $selcustom="";
  
   if($_POST['deskType']=="kde")
   {
      $selkde=" selected";
      $dis_DeskSettingButton=" disabled";
   }
   
   if($_POST['deskType']=="gnome")
   {
      $selgnome=" selected";
      $dis_DeskSettingButton=" disabled";
   }
   
   if($_POST['deskType']=="cde")
   {
      $selcde=" selected";
      $dis_DeskSettingButton=" disabled";
   }
   
   if($_POST['deskType']=="xdm")
   {
     $selxdm=" selected";
     $dis_DeskSettingButton="";
   }     
   
   if($_POST['deskType']=="custom")
   {
     $selcustom=" selected";
     $dis_DeskSettingButton="";
    
   }      

$optionsDesk='<option value="kde" '.$selkde.'>KDE</option>
            <option value="gnome"'.$selgnome.'>GNOME</option>
            <option value="cde"'.$selcde.'>CDE</option>
            <option value="xdm"'.$selxdm.'>XDM</option>
            <option value="custom"'.$selcustom.'>Custom</option>';
 }

 if($_POST['sessionType']=="windows")
 {
  $selWin=" selected";
  $optionsDesk='<option value="rdp">RDP</option>';
  $dis_deskType=" disabled";  
  $dis_DeskSettingButton="";
 }

 if($_POST['sessionType']=="vnc")
 {
  $selVnc=" selected";
  $optionsDesk='<option value="vnc">RFB</option>';
  $dis_deskType=" disabled";  
  $dis_DeskSettingButton="";
 }
 
 if($_POST['sessionType']=="shadow")
 {
  $selShadow=" selected";
  $optionsDesk='<option value="nx" >NX</option>';
  $dis_deskType=" disabled";  
  $dis_DeskSettingButton=" disabled";  
 }  

 $iconSet=$_POST['icon'];

 if(isset($_POST['CustomDisplaySettings']))
 {
  $check_CustomDisplaySettings=" checked";
  $dis_displaySetting="";
 }
 else
 {
  $dis_displaySetting=" disabled";
 }
  
 //
 // check for XDM panel
 // 
 
 $check_xdmMode1="";
 $check_xdmMode2="";
 $check_xdmMode3="";
 $check_xdmMode4="";

 $dis_XdmPortQuery=" disabled";
 $dis_XdmDomainQuery=" disabled";
 $dis_XdmPortList=" disabled";
 $dis_XdmDomainList=" disabled";
 $dis_XdmPortBroadCast=" disabled";
 
 $XdmDomainQueryValue="localhost";
 $XdmPortQueryValue="177"; 
 $XdmDomainListValue="localhost";
 $XdmPortListValue="177"; 
 $XdmPortBroadCastValue="177";
 
 

 
 if($_POST['xdmMode']=="server decide")
 {
  $check_xdmMode1=" checked";
 }
 elseif($_POST['xdmMode']=="query")
 {
  $check_xdmMode2=" checked";
 
  $dis_XdmPortQuery="";
  $dis_XdmDomainQuery="";
 
  $XdmDomainQueryValue=$_POST['XdmDomainQuery'];
  $XdmPortQueryValue=$_POST['XdmPortQuery']; 
 }
 elseif($_POST['xdmMode']=="broadcast")
 {
  $check_xdmMode3=" checked";
  
  $dis_XdmPortBroadCast="";
   
  $XdmPortBroadCastValue=$_POST['XdmPortBroadCast'];

 }
 elseif($_POST['xdmMode']=="list")
 {
  $check_xdmMode4=" checked";

  $dis_XdmPortList="";
  $dis_XdmDomainList="";
 
  $XdmDomainListValue=$_POST['XdmDomainList'];
  $XdmPortListValue=$_POST['XdmPortList']; 
 }


 //windows panel
 
 if(isset($_POST['WindowServer'])) $WindowServerValue=$_POST['WindowServer']; 
 if(isset($_POST['WindowDomain'])) $WindowDomainValue=$_POST['WindowDomain'];
 if(isset($_POST['usernameWindows']))    $usernameWindowsValue=$_POST['usernameWindows'];
 if(isset($_POST['passwordWindows']))    $passwordWindowsValue=$_POST['passwordWindows'];
 if(isset($_POST['applicationWindows']) && get_magic_quotes_gpc()== true)
 {
  $applicationWindowsValue=stripslashes($_POST['applicationWindows']);
 }
 else
 {
  $applicationWindowsValue=$_POST['applicationWindows'];
 }
 
 $dis_RememberPasswordWindows=" disabled";
 $dis_usernameWindows =" disabled";
 $dis_passwordWindows=" disabled";
 
 $check_RememberPasswordWindows=" checked";
 if(isset($_POST['runWindowsType']))
 {
  if($_POST['runWindowsType']=="false")
  {
   $check_runWindowsType0=" checked";
   $check_runWindowsType1=" ";
   $dis_applicationWindows=" disabled";
  }
  elseif($_POST['runWindowsType']=="true")
  {
   $check_runWindowsType0=" ";
   $check_runWindowsType1=" checked";
   $dis_applicationWindows=" ";
  }
 } 
 if($_POST['RdpCredential'] == '2')
 {
  $check_RdpCredential2=" checked";
  $check_RememberPasswordWindows=" checked";
 }
 elseif($_POST['RdpCredential'] == '0')
 {
  $check_RdpCredential0=" checked";
  $dis_RememberPasswordWindows="";
  $dis_usernameWindows ="";
  $dis_passwordWindows="";
  if(isset($_POST['RememberPasswordWindows'])) 
  {
   $check_RememberPasswordWindows=" checked"; 
  }
 }
 elseif($_POST['RdpCredential'] == '1')
 {
  $check_RdpCredential1=" checked";
  $dis_RememberPasswordWindows=" disabled";
  $dis_usernameWindows =" disabled";
  $dis_passwordWindows=" disabled";
  $dis_WindowDomain=" disabled";
 }
 
 
 //
 // windows display settings
 //
 
 $check_winComp2="";
 $check_winComp0="";
 $check_winComp3="";
 $check_winComp1="";
 $check_useRdpJpg="";
 $dis_useRdpJpg=" ";
 
 $jpegQualityRdpValue="6";
 
 if($_POST['winComp']=='2') 
 {
  $check_winComp2=" checked";
 }
 elseif($_POST['winComp']=='1') 
 {
  $check_winComp1=" checked";
 }
 elseif($_POST['winComp']=='0')
 {
  $check_winComp0=" checked";
  $dis_useRdpJpg=" disabled";
 }
 elseif($_POST['winComp']=='3')
 {
  $check_winComp3=" checked";
  $dis_useRdpJpg=" disabled";
 }
 
 if(isset($_POST['useRdpJpg']))
 {
   $check_useRdpJpg=" checked";
   $jpegQualityRdpValue=$_POST['jpegQualityRdp'];
 }
 
 if(isset($_POST['rdpCache']))
 {
  $check_rdpCache=" checked";
 }

 $rdpQualityValue=$_POST['rdpQuality'];
//
// vnc panel
//

if(isset($_POST['vncRememberPassword']))
{ 
 $check_vncRememberPassword=" checked";
}

$vncPasswordValue=$_POST['vncPassword'];
$VncPortValue=$_POST['VncPort'];
$VncHostValue=$_POST['VncHost']; 
 


//
// vnc display options
//


$check_encodType0=" ";
$check_encodType1=" ";
$check_encodType2=" ";
$check_encodType3=" ";
$check_useVncJpg=" ";
$dis_useVncJpg=" ";
$jpegQualityVncValue="6";

 if($_POST['encodType']=='0')
 {
  $check_encodType0=" checked";
 }
 elseif($_POST['encodType']=='1')
 {
  $check_encodType1=" checked";
 }
 if($_POST['encodType']=='2')
 {
  $check_encodType2=" checked";
  $dis_useVncJpg=" disabled";
 }
 if($_POST['encodType']=='3')
 {
  $check_encodType3 = " checked";
  $dis_useVncJpg = " disabled";
 }
 if(isset($_POST['useVncJpg']))
 {
  $check_useVncJpg = " checked";
  $jpegQualityVncValue = $_POST['jpegQualityVnc'];
 }

//
// unix display options
//

if(isset($_POST['renderExt']))    $check_renderExt=" checked";
if(isset($_POST['backingStore'])) $check_backingStore=" checked";
if(isset($_POST['composite']))    $check_composite=" checked";
if(isset($_POST['sharedPix']))    $check_sharedPix=" checked";
if(isset($_POST['shm']))
{
 $check_shm=" checked"; 
 $dis_sharedPix=" disabled"; 
 
} 

if(isset($_POST['compressionType']))
{
if($_POST['compressionType']=='1')
{
 $check_compressionType1=" checked";
 
 if(isset($_POST['enableJpegQuality']))
 {
  $check_enableJpegQuality=" checked";
  $jpegQualityValue=$_POST['jpegQuality'];
 } 
 else
 {
  $jpegQualityValue="6";
 }
}
elseif($_POST['compressionType']=='0')
{
 $check_compressionType0=" checked";
 $dis_enableJpegQuality=" disabled";
 $jpegQualityValue="6";
}
elseif($_POST['compressionType']=='2')
{
 $check_compressionType2=" checked";
 $dis_enableJpegQuality=" disabled";
 $jpegQualityValue="6";
}
elseif($_POST['compressionType']=='3')
{

 $check_compressionType3=" checked";
 
 if(isset($_POST['enableJpegQuality']))
 {
  $check_enableJpegQuality=" checked";
  $jpegQualityValue=$_POST['jpegQuality'];
 } 
 else
 {
  $jpegQualityValue="6";
 }
}

}



 // 
 // check for HttpProxy panel
 //

 $proxyHostValue     =$_POST['proxyHost'];
 $proxyPortValue     =$_POST['proxyPort'];  
 $userNameProxyValue =$_POST['userNameProxy'];  
 $passwordProxyValue =$_POST['passwordProxy'];


 if(isset($_POST['rememberProxyAuth']))
 {  
  $check_rememberProxyAuth=" checked";
 }
    
 




 // 
 // check for advanced panel
 //
 if(isset($_POST['HttpProxy']))
 {
   $check_HttpProxy=" checked"; 
   $dis_setHttpProxy="";
 }
 else
 {
   $dis_setHttpProxy=" disabled";
 }

 if(isset($_POST['lazyEncoder']))
 {
   $check_lazyEncoder=" checked";
 }
 
 if(isset($_POST['grabKeyboard']))
 {
   $check_grabKeyboard=" checked";
 }

 if(isset($_POST['directDraw']))
 {
   $check_directDraw=" checked";
 }
 
 if(isset($_POST['NoDelayTcp']))
 {
   $check_NoDelayTcp=" checked";
 }

 if(isset($_POST['NoZlibStreamCompression']))
 {
   $check_NoZlibStreamCompression=" checked";
 }

 $check_EnableSSL=" ";
 if(isset($_POST['EnableSSL']))
 {
   $check_EnableSSL=" checked";
 }   




 // 
 // check for services panel
 //
 if(isset($_POST['EnableMultimedia']))
 {
   $check_EnableMultimedia=" checked";
 }


 // 
 // check for environment panel
 //
 if(isset($_POST['useFontServer']) && ($_POST['useFontServer']=="true")  ) 
 {
  $fontServerHostValue=$_POST['fontServerHost'];
  $fontServerPortValue=$_POST['fontServerPort'];
  $check_useFontServer=" checked";
  $dis_fontServerHost="";
  $dis_fontServerPort="";
 } 
 else
 {
  $dis_fontServerHost="disabled";
  $dis_fontServerPort="disabled";
 }

  //check for custom panel 
   
  $check_taint ="";
  $dis_taint =" disabled";
  $dis_disableX ="";
  $check_disableX ="";
  $check_customOptions0 =" ";
  $check_customOptions1="";
  $dis_customCommand =" disabled";
  $check_runApplication0="";
  $check_runApplication1="";
  $check_runApplication2="";
  $customCommandValue="";
  
  if($_POST['runApplication'] == 'console')
  {
   $check_runApplication0=" checked";
  }
  elseif($_POST['runApplication'] == 'default')
  {
   $check_runApplication1=" checked";
  } 
  elseif($_POST['runApplication'] == 'application')
  {
   $check_runApplication2=" checked";
   $dis_customCommand =" ";
   $customCommandValue=$_POST['customCommand'];
  } 
  if($_POST['customOptions'] == 'false')
  {
   $check_customOptions0 =" checked";
   $dis_taint =" disabled";
   
  }
  if($_POST['customOptions'] == 'true')
  {
   $check_customOptions0 =" ";
   $check_customOptions1 =" checked";
   $dis_taint =" disabled";
   $dis_disableX =" disabled";
  }
  
  if(isset($_POST['disableX']))
  {
   if($_POST['disableX'] == 'false')
   {
    $check_disableX =" checked";
    $dis_taint =" ";
   }
   else
   {
    $check_disableX =" ";
    $dis_taint =" disabled";
   }
  } 
  if(isset($_POST['taint']))
  {
   if($_POST['taint'] == 'false')
   {
    $check_taint =" checked";
   }
   else
   {
    $check_taint =" ";
   }
  }
  
  
}
else //set default fields values with default
{


 // 
 // set default for HttpProxy panel
 //

 $proxyHostValue     ="";
 $proxyPortValue     ="";  
 $userNameProxyValue ="";  
 $passwordProxyValue ="";

 $check_rememberProxyAuth="";


 // 
 // set default for advanced panel
 //

$check_HttpProxy=" ";
$check_lazyEncoder=" ";
$check_NoDelayTcp=" "; 
$check_NoZlibStreamCompression=" "; 
$check_EnableSSL="";
$dis_setHttpProxy=" disabled";

 // 
 // set default for services panel
 //
 $check_EnableMultimedia="";




 // 
 // set default for environment panel
 //

 $check_useFontServer="";
 $dis_fontServerHost="disabled";
 $dis_fontServerPort="disabled";
 $fontServerHostValue="";
 $fontServerPortValue="";


 // 
 // set default  for general panel
 //



$optionsDesk='<option value="kde">KDE</option>
          <option value="gnome">GNOME</option>
          <option value="cde">CDE</option>
          <option value="xdm">XDM</option>
          <option value="custom">Custom</option>';
$dis_DeskSettingButton=" disabled";
$iconSet="";

$check_CustomDisplaySettings="";

$dis_displaySetting=" disabled";

// xdm panel

 $check_xdmMode1=" checked";
 $check_xdmMode2="";
 $check_xdmMode3="";
 $check_xdmMode4="";

 $dis_XdmPortQuery=" disabled";
 $dis_XdmDomainQuery=" disabled";
 $dis_XdmPortList=" disabled";
 $dis_XdmDomainList=" disabled";
 $dis_XdmPortBroadCast=" disabled";
 
 $XdmDomainQueryValue="localhost";
 $XdmPortQueryValue="177"; 
 $XdmDomainListValue="localhost";
 $XdmPortListValue="177"; 
 $XdmPortBroadCastValue="177";
 
 //windows panel
 
 $WindowServerValue=""; 
 $WindowDomainValue="";
 $dis_RememberPasswordWindows=" disabled";
 $dis_usernameWindows =" disabled";
 $dis_passwordWindows=" disabled";
 $check_RememberPasswordWindows=" checked";
 $check_RdpCredential2=" checked";
  if(!isset($_POST['runWindowsType']))
 {
 $check_runWindowsType0=" checked";
 $check_runWindowsType1=" ";
 $dis_applicationWindows=" disabled";
 }
 elseif(isset($_POST['runWindowsType']))
 {
  if($_POST['runWindowsType']=="false")
  {
   $check_runWindowsType0=" checked";
   $check_runWindowsType1=" ";
   $dis_applicationWindows=" disabled";
  }
  elseif($_POST['runWindowsType']=="true")
  {
   $check_runWindowsType0=" ";
   $check_runWindowsType1=" checked";
   $dis_applicationWindows=" ";
  }
 }

 // windows display settings
 
 $check_winComp3=" checked";
 $check_winCompMaps=" checked";
 $check_rdpCache=" checked";
 $dis_winCompMaps="";
 $rdpQualityValue='0';
 $jpegQualityRdpValue="6";

 
//vnc display settings

$check_encodType3=" checked";
$jpegQualityVncValue="6";

//unix display settings

$jpegQualityValue="6";
$check_compressionType3=" checked"; 



//custom panel
 
$check_taint ="";
$dis_taint =" disabled";
$dis_disableX ="";
$check_disableX ="";
$check_customOptions0 =" checked";
$check_customOptions1="";
$dis_customCommand =" disabled";
$check_runApplication0=" checked";
$check_runApplication1="";
$check_runApplication2="";


$selUnix="";
$selWin="";
$selVnc="";
$selShadow="";

}


?>
<? include("includes/Top.php");?>

<center>
<!--[if IE 6]>
<script type="text/javascript">
function calcWidth() {
document.getElementById('min_width1').style.width = (document.body.clientWidth < 911 ? '910px' : '100%')
}
onresize = calcWidth;
</script>
<![endif]-->
<? include("includes/Toolbar.php");?>

<script language="javascript" src="js/drag.js"></script>
<script>
  function setBackgroundTab(elem)
{
  var Tab0=document.getElementById('im0');
  var Tab1=document.getElementById('im1');
  var Tab2=document.getElementById('im2');
  var Tab3=document.getElementById('im3');
  
  var normal="<a class=\"normal\" href=\"#\">";
  var close="</a>";
  var closeSpan="</span>";
  
  var visited="<span class=\"visited\">";
  
  switch (elem.id) 
  {
   case "tab0":
     
   Tab0.innerHTML=visited+'General'+closeSpan;
   Tab1.innerHTML=normal+'Advanced'+close;
   Tab2.innerHTML=normal+'Services'+close;
   Tab3.innerHTML=normal+'Environment'+close;
 
   break;

   case "tab1":
   
   Tab1.innerHTML=visited+'Advanced'+closeSpan;
   
   
   Tab0.innerHTML=normal+'General'+close;
   Tab2.innerHTML=normal+'Services'+close;
   Tab3.innerHTML=normal+'Environment'+close;
  
   break;

   case "tab2":

   Tab2.innerHTML=visited+'Services'+closeSpan;
   
   
   Tab0.innerHTML=normal+'General'+close;
   Tab1.innerHTML=normal+'Advanced'+close;
   Tab3.innerHTML=normal+'Environment'+close;

   break;
  
   case "tab3":

   Tab3.innerHTML=visited+'Environment'+closeSpan;
  
   Tab0.innerHTML=normal+'General'+close;
   Tab1.innerHTML=normal+'Advanced'+close;
   Tab2.innerHTML=normal+'Services'+close;
   break; 
   
   default:
   break;
  }
}

</script>
<!--SectionIcon Start-->
<div id="min_width1">
<table border="0" cellspacing="0" cellpadding="0" class="header3" width="100%" style="min-width:900px;">
<tr>
 <td><img src="<?=$sharedSkin?>/icon-addSession.png" width="34" height="26" border="0"></td>
 <td class="txtHeader3" nowrap align="left">Add Session</td>
</tr>
</table>
</div>

<form name="addSession" action="<?=$_SERVER['PHP_SELF']?>" method="post" ENCTYPE="multipart/form-data" >
<!--START structure table -->
<table border="0" width="98%" cellspacing="0" cellpadding="0" style="min-width:900px; margin:0px;margin-top:15px;">
<tr>
 <!-- START details -->
 <td valign="top" align="left">
   <table border="0" width="100%" cellspacing="0" cellpadding="0" class="bgHeaderTable">
    <tr align="left">
     <td height="26" class="txtHeaderTable">&nbsp;Details</td>
    </tr>
   </table>
   
 <table border="0" width="100%" cellspacing="0" cellpadding="0" class="bgBoxInternal">
 <tr align="left">
  <td valign="top" style="padding:5px;">
  
    <table border="0" cellpadding="0" cellspacing="0" width="400" class="heightAddSession">
     <tr>
      <td style="padding: 5px;" align="left" valign="top" width="100%">
       
       <table border="0" cellpadding="2" cellspacing="0">
       <tr>
        <td colspan="3"><img src="<?=$shared?>/empty.png" border="0" height="1" width="246"></td>
       </tr>
       <tr>  
        <td class="txtLabel" style="padding: 5px;" align="right" nowrap="nowrap">Session name:</td>
        <td colspan="2" align="left"><input name="sessionName" size="49" class="textBlack" type="text" value="<?=$_POST['sessionName']?>"></td>
       </tr>
       <tr>  
        <td class="txtLabel" style="padding: 5px;" align="right" nowrap="nowrap">Server name:</td>
        <td class="txtLabel" colspan="2" align="left" nowrap="nowrap">
         <select name="serverId" id="idServer" class="textBlack" style="width: 260px;" onchange="javascript: setDescrition('resp.php')">
         
        <?
         //
         //return a server list 
         //    
         
         if(!isset($_POST['serverId']))
         {
          $_POST['serverId']="";
         }
              
         $serverList=getServer();
      
         if($serverList)
         {
          while($server  = mysql_fetch_array($serverList))
          {
           $selected="";
           if($_POST['serverId']==$server['id']) $selected=" selected";
           
           echo "<option value=\"".$server['id']."\" $selected >".$server['serverName']."</option>\n";
          }
          
         }
         
        ?>
         </select> 
        </td>
        </tr>
        </table>
        <div id="result"> 
        <?
         $descList  = getServer($_POST['serverId']);
         $desc  = mysql_fetch_array($descList);
        ?>
          <table id="hostDescription"  border="0" cellpadding="2" cellspacing="0">
          <tr>  
           <td class="txtLabel" style="padding: 5px; width:84px;" align="right"> Host: </td>
           <td class="txtValue" align="left" nowrap="nowrap"><?=$desc['hostName']?></td>
           <td class="txtLabel" align="left" nowrap="nowrap"> Port:  &nbsp; <?=$desc['port']?></td>
          </tr>
          <tr>  
           <td class="txtLabel" style="padding: 5px;" align="right" valign="top"> Description: </td>
           <td class="txtValue" colspan="2" align="left"> 
            <?=formatDesc($desc['description'])?>
           </td>
          </tr>
          <tr>  
           <td class="txtLabel" style="padding: 5px;" align="right" nowrap="nowrap"> O.S.: </td>
           <td class="txtValue" colspan="2" align="left"><?=$desc['OS']?></td>
          </tr>
          <tr>  
           <td class="txtLabel" style="padding: 5px;" align="right" nowrap="nowrap"> CPU: </td>
           <td class="txtValue" colspan="2" align="left"><?=$desc['CPU']?></td>
          </tr>
          <tr>  
           <td class="txtLabel" style="padding: 5px;" align="right" nowrap="nowrap"> Memory: </td>
           <td class="txtValue" colspan="2" align="left"><?=$desc['memory']?></td>
          </tr>
          <tr>  
           <td class="txtLabel" style="padding: 5px;" align="right" nowrap="nowrap"> Disk: </td>
           <td class="txtValue" colspan="2" align="left"><?=$desc['disk']?></td>
          </tr>
          </table>
       </div>
      </td>
     </tr>
     </table>
  
     
    </td>
   </tr>
   </table>
   <!-- STOP details -->
 </td>
 <td width="25">&nbsp;</td>
 <!-- START box dx -->
 <td valign="top" align="center" WIDTH="484">

     <!--  start tab  -->

     <table BORDER=0 CELLPADDING="0" CELLSPACING="0" WIDTH="484" align="center">
     <tr height="27">
      <td>
       <div id="tab0" class="tabL"  style="z-index: 1;" onclick="setBackgroundTab(this);selectTab(0,4);setlayer(0,1);">
         <span id="im0" style="margin:0px; padding: 0px;"><span class="visited">General</span></span>
       </div>
      </td>
      <td>
        <div id="tab1" class="tabL" style="z-index: 1;" onclick="setBackgroundTab(this);selectTab(1,4);setlayer(1,1);">
         <span id="im1" style="margin:0px;"><a class="normal" href="#">Advanced</a></span>
        </div>
      </td>
      <td>
        <div id="tab2" class="tabL" style="z-index: 1;" onclick="setBackgroundTab(this);selectTab(2,4);setlayer(2,1);" >
         <span id="im2" style="margin:0px;"><a class="normal" href="#">Services</a></span>
        </div>
      </td>
      <td>
        <div id="tab3" class="tabL" style="z-index: 1;" onclick="setBackgroundTab(this);selectTab(3,4);setlayer(3,1);">
         <span id="im3" style="margin:0px;"><a class="normal" href="#">Environment</a></span>
        </div>
      </td>       
     </tr>
     </table>


     <!-- end tab -->

     <!-- start nx_box_right -->
      <? include("includes/box.php");?>
     <!-- stop nx_box_right -->

 </td>
</tr>
</table>

<? if($messageType!="") { ?>
<div class="message">
<p class="t_2orange" style="margin:0px;padding:0px;line-height:27px;">
 <img src="<?=$sharedSkin?>/icon_alertWhite.png" align="middle">
 <?=$Message[$messageType]?>
</p></div>
<? } ?>


 
 <table cellSpacing="0" cellPadding="0" width="98%" height="27" class="bgMiniBar">
  <tr>
   <td width="100%"></td>
   <td width="100" align="center"><input type="image" name="addSession" width="82" height="17" style="cursor: pointer;margin-right:10px;" src="<?=$button?>/b_add.png" border="0"  onmouseout="this.src='<?=$button?>/b_add.png'" onmouseover="this.src='<?=$button?>/b_addOver.png'"></td>
  </tr>
 </table>

<div id="ghost" style="position: absolute; background:transparent url('<?=$shared?>/empty.png');border: 0px solid #000; width: 100%; top: 0px; left: 0px; display: none; z-index: 5;"></div> 
  
   
   <div id="winCustomSetting" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
    <td id="handleWinCustom" class="handle"><span id="displayWinCustomTitle" class="txtHeaderTable"></span>
   	</td>
   	<td>
   	<a  onclick="javascript: document.getElementById('winCustomSetting').style.display='none'; disableAction();">
    <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
    </a></td>
    </tr></table>
     <div class="contentPanel">
        <? include ("winCustom.php"); ?>
     </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>
   
   
   
   
   <div id="unixCustomSetting" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleUnixCustom" class="handle" >  
   	  <span id="displayUnixCustomTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('unixCustomSetting').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("unixCustom.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>

   <div id="vncCustomSetting" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleVncCustom" class="handle" >  
   	  <span id="displayVncCustomTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('vncCustomSetting').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("vncCustom.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>

   <div id="proxyOption" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleProxy" class="handle" >  
   	  <span id="proxyTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('proxyOption').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("proxyPanel.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>

   
   <div id="winOption" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleWin" class="handle" >  
   	  <span id="winTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('winOption').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("WindowsPanel.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>

   <div id="vncOption" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleVnc" class="handle" >  
   	  <span id="vncTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('vncOption').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("vncPanel.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>

   <div id="xdmOption" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleXdm" class="handle" >  
   	  <span id="xdmTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('xdmOption').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("xdmPanel.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>   



   <div id="customOption" class="box_pop_up" style="left: 383px; top: 107px;">
   <table width="100%" cellpadding="0" padding="0" border="0" class="bg">
   <tr>
     <td id="handleCustom" class="handle" >  
   	  <span id="customTitle"  class="txtHeaderTable"></span>
     </td>
   	 <td>
   	  <a  onclick="javascript: document.getElementById('customOption').style.display='none'; disableAction();">
       <img src="<?=$button?>/b_close.png" onmouseout="this.src='<?=$button?>/b_close.png'" onmouseover="this.src='<?=$button?>/b_closeOver.png'">
      </a>
     </td>
    </tr>
    </table>
    <div class="contentPanel">
        <? include ("customPanel.php"); ?>
    </div><!--[if lte IE 6.5]><iframe></iframe><![endif]-->	
   </div>


 <script language="javascript">
	var limitW=document.body.clientWidth-300;
	var limitH=document.body.clientHeight-200;

	var cusUnixHandle = document.getElementById("handleUnixCustom");
	var cusUnixRoot   = document.getElementById("unixCustomSetting");
	Drag.init(cusUnixHandle, cusUnixRoot, 0, limitW, 0, limitH);


	var cusVncHandle = document.getElementById("handleVncCustom");
	var cusVncRoot   = document.getElementById("vncCustomSetting");
	Drag.init(cusVncHandle, cusVncRoot, 0, limitW, 0, limitH);
	var hW = document.getElementById("handleWinCustom");
	var RoTSet   = document.getElementById("winCustomSetting");
	Drag.init(hW, RoTSet, 0, limitW, 0, limitH);

	var theHandleWin = document.getElementById("handleWin");
	var theRootWin   = document.getElementById("winOption");
	Drag.init(theHandleWin, theRootWin, 0, limitW, 0, limitH);

	var theHandleVnc = document.getElementById("handleVnc");
	var theRootVnc   = document.getElementById("vncOption");
	Drag.init(theHandleVnc, theRootVnc, 0, limitW, 0, limitH);	

	var theHandleXdm = document.getElementById("handleXdm");
	var theRootXdm   = document.getElementById("xdmOption");
	Drag.init(theHandleXdm, theRootXdm, 0, limitW, 0, limitH);

	var theHandleCustom = document.getElementById("handleCustom");
	var theRootCustom   = document.getElementById("customOption");
	Drag.init(theHandleCustom, theRootCustom, 0, limitW, 0, limitH);
	
	var theHandleProxy = document.getElementById("handleProxy");
	var theRootProxy   = document.getElementById("proxyOption");
	Drag.init(theHandleProxy, theRootProxy, 0, limitW, 0, limitH);	

 </script>

</form>
</center>

<? include("includes/Footer.php") ?>
