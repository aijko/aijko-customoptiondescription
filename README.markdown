A new field in the backend lets you specify a description for a custom option to separate it from the option's title. In your templates you could then access this information like this: The example is based on the template file

    design/frontend/base/default/template/catalog/product/view/options/type/text.phtml

and can be used for the other option types as well.

You'll probably want to copy this template file to your custom template and then add this code snippet at line 29:

~~~~~~ php
<?php if ($_option->getDescription()): ?><span style="margin-left: 20px;"><?php echo $this->htmlEscape($_option->getDescription()) ?></span><?php endif; ?>
~~~~

With the surrounding code it may look like this:

~~~~~~ php
<dt><label<?php if ($_option->getIsRequire()) echo ' class="required"' ?>><?php if ($_option->getIsRequire()) echo '<em>*</em>' ?><?php echo  $this->htmlEscape($_option->getTitle()) ?></label>
    <?php if ($_option->getDescription()): ?><span style="margin-left: 20px;"><?php echo $this->htmlEscape($_option->getDescription()) ?></span><?php endif; ?>
    <?php echo $this->getFormatedPrice() ?></dt>
~~~~~
