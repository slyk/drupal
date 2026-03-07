# Input Validation Implementation Plan for UserFull Window (Simplified)

## Overview

Simple implementation of real-time input validation for fname, surname, and company fields in UserFull window. All validation logic goes directly in UserFullMediator - no separate classes.

## Requirements

### Fields to Validate
- `uname` (fname) - Label: "Имя:"
- `usurname` (surname) - Label: "Фамилия:"
- `ucompany` (company) - Label: "Организация:"

### Allowed Characters
- Cyrillic letters (Ukrainian/Russian)
- Latin letters (a-z, A-Z)
- Digits (0-9)
- Hyphen (-), underscore (_), space

### Behavior
1. Block invalid characters as user types
2. Play error sound (`SoundProxy.ERROR`)
3. Show console warning with blocked chars and field name
4. Cursor moves to end after inserting allowed chars (acceptable trade-off for simplicity)

---

## Implementation

### Single File to Modify: `view/full/UserFullMediator.as`

#### 1. Add imports (if not already present)
```actionscript
import flash.events.TextEvent;
import model.SoundProxy;
import model.vo.helpers.RFC3164;
```

#### 2. Add regex pattern as class constant
```actionscript
// Allowed: Cyrillic, Latin, digits, hyphen, underscore, space
private static const ALLOWED_CHARS_PATTERN:RegExp = /^[a-zA-Z0-9\u0400-\u04FF\u0500-\u052F\-_\s]*$/;
```

#### 3. Add event listeners in `_initView()` method (after existing listeners around line 207)
```actionscript
// Add text input validation listeners
vw.uname.addEventListener(TextEvent.TEXT_INPUT, _onTextInputValidate);
vw.usurname.addEventListener(TextEvent.TEXT_INPUT, _onTextInputValidate);
vw.ucompany.addEventListener(TextEvent.TEXT_INPUT, _onTextInputValidate);
```

#### 4. Add single validation handler method
```actionscript
/**
 * Validate text input in real-time - block invalid characters
 * Simple implementation: appends allowed chars to end of text
 */
private function _onTextInputValidate(event:TextEvent):void
{
    var insertedText:String = event.text;
    if (!insertedText || insertedText.length == 0) return;
    
    // Check each character in the inserted text
    var blockedChars:Array = [];
    var allowedText:String = "";
    
    for (var i:int = 0; i < insertedText.length; i++)
    {
        var char:String = insertedText.charAt(i);
        if (ALLOWED_CHARS_PATTERN.test(char))
            allowedText += char;
        else
            blockedChars.push(char);
    }
    
    // If all characters are allowed, let default behavior proceed
    if (blockedChars.length == 0) return;
    
    // Prevent default insertion
    event.preventDefault();
    
    // Get field label
    var fieldLabel:String = "";
    var input:Object = event.target;
    if (input == vw.uname) fieldLabel = "Ім'я";
    else if (input == vw.usurname) fieldLabel = "Прізвище";
    else if (input == vw.ucompany) fieldLabel = "Організація";
    
    // If there are allowed characters, append them to the text
    if (allowedText.length > 0)
    {
        input.text = input.text + allowedText;
    }
    
    // Play error sound
    SoundProxy.play(SoundProxy.ERROR);
    
    // Format blocked characters for display
    var blockedDisplay:String = blockedChars.join("', '");
    
    // Send console warning
    sendNotification(MVCConst.N_CONSOLE, {
        msg: "Заблоковано символи ('" + blockedDisplay + "') у полі '" + fieldLabel + "'",
        type: RFC3164.MSG_WARNING
    });
}
```

---

## Summary of Changes

| File | Change |
|------|--------|
| `view/full/UserFullMediator.as` | Add imports, constant, event listeners, and handler method |

**No new files created.**

---

## Testing

- [ ] Type allowed characters (Latin, Cyrillic, digits, -, _, space) - should work normally
- [ ] Type blocked characters (@, !, $, etc.) - should be blocked with sound and console message
- [ ] Paste text with mixed characters - allowed chars should be appended, blocked chars rejected
- [ ] Test each field: fname, surname, company
