# CSS Variables
This list contains all CSS variables used in Voyagers stylesheet.  
Each variable (expect `font-family`) has a corresponding `-dark` variable.  
For example `bg-color` also exists as `bg-color-dark`


## Accent
- accent-bg-color
- accent-border-color
- accent-text-color

## Body
- bg-color
- font-family
- link-color
- text-color
- description-text-color
- label-text-color

## Code (HTML element)
- code-bg-color
- code-text-color

## Inputs
- input-bg-color
- input-border-color
- input-text-color

## Loader
- loader-bg-color
- loader-text-color

## Notifications
- notification-bg-color
- notification-message-color
- notification-title-color

## Sidebar
- sidebar-bg-color
- sidebar-border-color
- sidebar-icon-color
- sidebar-item-active-color
- sidebar-item-hover-color
- sidebar-item-icon-active-color
- sidebar-item-icon-color
- sidebar-item-text-active-color
- sidebar-item-text-color
- sidebar-sub-item-color
- sidebar-title-color

## Table
- table-border-color
- table-head-bg-color
- table-head-border-color
- table-head-text-color
- table-row-border-color
- table-row-even-bg-color
- table-row-even-bg-color-hover
- table-row-even-text-color
- table-row-even-text-color-hover
- table-row-odd-bg-color
- table-row-odd-bg-color-hover
- table-row-odd-text-color
- table-row-odd-text-color-hover

## Tooltip
- tooltip-arrow-border-color
- tooltip-bg-color
- tooltip-border-color
- tooltip-text-color


For convenience, here is a usable stylesheet:

```css
:root {
    // Accent
    --accent-bg-color: 0xFF;
    --accent-border-color: 0xFF;
    --accent-text-color: 0xFF;

    // Body
    --bg-color: 0xFF;
    --font-family: Inter-Ui;
    --link-color: 0xFF;
    --text-color: 0xFF;
    --description-text-color: 0xFF;
    --label-text-color: 0xFF;

    // Code (HTML element)
    --code-bg-color: 0xFF;
    --code-text-color: 0xFF;

    // Inputs
    --input-bg-color: 0xFF;
    --input-border-color: 0xFF;
    --input-text-color: 0xFF;

    // Loader
    --loader-bg-color: 0xFF;
    --loader-text-color: 0xFF;

    // Notifications
    --notification-bg-color: 0xFF;
    --notification-message-color: 0xFF;
    --notification-title-color: 0xFF;

    // Sidebar
    --sidebar-bg-color: 0xFF;
    --sidebar-border-color: 0xFF;
    --sidebar-icon-color: 0xFF;
    --sidebar-item-active-color: 0xFF;
    --sidebar-item-hover-color: 0xFF;
    --sidebar-item-icon-active-color: 0xFF;
    --sidebar-item-icon-color: 0xFF;
    --sidebar-item-text-active-color: 0xFF;
    --sidebar-item-text-color: 0xFF;
    --sidebar-sub-item-color: 0xFF;
    --sidebar-title-color: 0xFF;

    // Table
    --table-border-color: 0xFF;
    --table-head-bg-color: 0xFF;
    --table-head-border-color: 0xFF;
    --table-head-text-color: 0xFF;
    --table-row-border-color: 0xFF;
    --table-row-even-bg-color: 0xFF;
    --table-row-even-bg-color-hover: 0xFF;
    --table-row-even-text-color: 0xFF;
    --table-row-even-text-color-hover: 0xFF;
    --table-row-odd-bg-color: 0xFF;
    --table-row-odd-bg-color-hover: 0xFF;
    --table-row-odd-text-color: 0xFF;
    --table-row-odd-text-color-hover: 0xFF;

    // Tooltip
    --tooltip-arrow-border-color: 0xFF;
    --tooltip-bg-color: 0xFF;
    --tooltip-border-color: 0xFF;
    --tooltip-text-color: 0xFF;
}
```