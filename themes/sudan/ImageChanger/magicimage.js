///////////////////////////////////////////////////////////
/// Magic Image Rotation								///
///	v 1.0.1												///
/// Copyright 2007, Loyce Bradley Petrey				///
///	All Rights Reserved.								///
///////////////////////////////////////////////////////////

var ImageLoad = 
[																				
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum2.jpg', 	''	],		//  ['URL to linked page', 'URL to image', 'Caption under picture']	//
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum3.jpg', 	''	],		//  The caption is not required and may be left blank like this:	//
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum4.jpg', 	''	],		//  ['URL to linked page', 'URL to image', '']						//
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum5.jpg', 	''	],		//  Add as many images as you like seperated by commmas				//
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum6.jpg', 	''	],		//  Almost ALL errors are caused by the url or path being wrong		//
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum7.jpg', 	''	],		//  The LAST image declaration does NOT have a comma after it		//
['header.php', 	'themes/sudan/ImageChanger/flash/Referendum8.jpg', 	''	]
];

var ImageCount		= 7;			//  *****  Change this to the total number of images loaded above  ***** 		//	
var ImageDelay		= 10000;		//  *****  Set this to the delay interval desired.  5000 = 5 seconds.			// 
var LinkTarget		= "_self"		//  *****  Defines where you want linked page to open. _self, _blank, _top, etc	//
var ImageIndex		= 0;			//  DO NOT ALTER	//
var FirstLoad 		= 0;			//  DO NOT ALTER	//
var QuickStartID 	= 0;  			//  DO NOT ALTER	//
var htmlString 		= ""			//  DO NOT ALTER 	//

//  This function rotates the banner  //
function ImageChange()

{		

htmlString = '<center>';
htmlString = htmlString + '<font face = "Verdana" size="2">';		//  Font and Font Size for caption may be changed here	//
htmlString = htmlString +'<a target="';
htmlString = htmlString + LinkTarget;
htmlString = htmlString + '" href="';
htmlString = htmlString + ImageLoad[ImageIndex][0];
htmlString = htmlString + '"><img border="1" src="';				//  Image border size may be changed here				//	
htmlString = htmlString + ImageLoad[ImageIndex][1];
htmlString = htmlString + '"></a><br>';
htmlString = htmlString + ImageLoad[ImageIndex][2];
htmlString = htmlString + '</font>';
htmlString = htmlString + '</center>';		

document.getElementById('MagicImage').innerHTML = htmlString; 				

if(ImageIndex == ImageCount - 1)		//  This statement increments image displayed and resets if displaying last image  //
{										
ImageIndex= 0;																				
}																								
else																							
{																								
ImageIndex++;																					
}																										

if(FirstLoad == 0)						//  Determins if this is the first time function has run.   // 
{
SlowFinish();
}

}
//  End Funtion  //

//  This function ensures first banner is displayted without a delay  //
function  QuickStart()
{
QuickStartID=setInterval("ImageChange()", 1000);
}
//  End Funtion  //																		

//  This function sets display rate to user defined speed  //
function SlowFinish()
{
clearInterval(QuickStartID);
FirstLoad = 1;
setInterval("ImageChange()", ImageDelay);	 
}
//  End Funtion  //

QuickStart()