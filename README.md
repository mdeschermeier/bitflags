# BitFlags

A small class to compress down boolean flags into a single integer.

## General Info

### Installation

#### Composer
Probably the simplest way to get this up and running in your project. 

`composer require mdeschermeier\bitflags`

...and you're done!

#### Manual Install
Also pretty easy-peasy. Just drop the file located in the `src` directory (it's just the one file) wherever you want it to live and add the line:

`require_once "path/to/where/you/put/BitFlags.php";`

to the file you want to include this class into.

#### Namespace
Don't forget to use the namespace!

`use mdeschermeier\bitflags\BitFlags;`

### Contacting Me
The best place to get my attention is the Issue Tracker (if there's an issue, of course). If there isn't anything broken, but are looking for 
some more information or have some questions, please feel free to email me at miked.github@gmail.com.


## IMPORTANT!
While this class is more-or-less set it and forget it, there is one gotcha that I've identified that could potentially cause some problems.

**Order Matters:** This class utilizes associative arrays in order to allow you to reference the flags you set by name.
In order to make this work, each flag is assigned a bit position in the order it is processed - meaning that if you use this class to 
generate a number, store that number in a DB, then later attempt to instantiate the class again using that stored value, your array needs 
to match what it was in the first place or your numbers will be off.

**__For Example__**:

```
 $Arr1 = ['Flag_1' => true, 'Flag_2' => false, 'Flag_3' => true];
 $Arr2 = ['Flag_2' => false, 'Flag_1' => true, 'Flag_3' => true];
 
 $BF1 = new BitFlags($Arr1);
 $BF2 = new BitFlags($Arr2);
 
 $intVal1 = $BF1->getCompressedFlags();
 $intVal2 = $BF2->getCompressedFlags();
 
 echo "intVal1: ".$intVal1."\n";
 echo "intVal2: ".$intVal2."\n";
 ```
 
**__Output:__**
 
 ```
 intVal1: 5
 intVal2: 6
 ```

Things should be okay if you want to *add* to your flag array, however - just so long as you are appending your additions to the end of
your array.

Now, it should be noted that with the capability of manually setting the integer value of the flags with `setCompressedFlags()`, you *could*
probably alter your arrays however you like and just update the values if you wanted. I'm not saying I *recommend* this, but hey, I'm
a Readme doc, not a cop. Go nutsy if you're feelin' gutsy.


## Methods

### Constructor
   ###### **__$initFlagArray__** - Associative Array _(Optional)_
Initializes flags passed into the constructor. Left empty or passing `null` simply initializes an empty array. If an array is passed into the constructor that is too large for the system to handle (greater than 63 or 31 elements on 64-bit or 32-bit systems, respectively), an Exception is thrown.
   
   
### *void* setFlags($flagArray)
###### **__$flagArray__** - Associative Array
Assigns flags specified in `$flagArray` to a bit and sets the bit to the appropriate value, as determined by the `$flagArray` element.
Will throw an Exception if the number of `$flagArray` elements exceed the maximum value allowed by your system architecture (*64-bit:* 63, *32-bit:* 31).
   
   
### *mixed* getFlagPosition($flag)
   ###### **__$flag__** - String
Returns the bit position of the specified `$flag` parameter. Returns `false` if flag does not exist.


### *boolean* toggleFlag($flag)
   ###### **__$flag__** - String
Toggles the bit associated with the specified `$flag` parameter. Returns `true` on success, and `false` on failure.


### *mixed* getFlagSetting($flag)
   ###### **__$flag__** - String
Retrieves current stored setting for flag bit identified by `$flag`. Returns `null` in the event the flag does not exist.


### *void* enableAllFlags()
   ###### **__None__**
Does just what it says on the tin: Flips all assigned bits to `1`.


### *void* disableAllFlags()
   ###### **__None__**
*Also* does what the name implies: Flips all assigned bits to `0`.
   

### *integer* getCompressedFlags()
   ###### **__None__**
Returns integer value of all enabled/disabled flags.


### *boolean* setCompressedFlags()
   ###### **__None__**
Manually sets the integer value of all enabled/disabled flags.

   
