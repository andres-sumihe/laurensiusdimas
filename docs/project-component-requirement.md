# Project Component Requirements

This document outlines the technical and design requirements for the **Project Component**. This component is the core building block for displaying portfolio items in sections such as *Curated Projects*, *Corporate Projects*, and *Older Projects*.

## Core Requirements

1.  **Content Display**: The component must display the project's primary assets (images/videos), title, and a short description (subtitle/category).
2.  **Dynamic Layouts**: The visual arrangement of the media assets must be dynamic, determined by a `layout` configuration selected via the Admin Panel.
3.  **Zero-Gap Grid**: All media items within the component must be displayed in a strict grid layout with **no gaps** (0px margin/padding) between items, both horizontally and vertically.
4.  **Responsive Design**: While the layouts below describe the desktop view, the component must adapt gracefully to mobile screens (typically stacking or simplifying the grid).

## Layout Configurations

The Admin Panel allows selection of the following grid layouts. Each layout defines how the uploaded media items are arranged.

### 1. Single (Hero)
A single, full-width media item. Ideal for projects with one strong hero image or video.
```
+------------------------------------------------------+
|                                                      |
|                      [ Image 1 ]                     |
|                                                      |
+------------------------------------------------------+
```

### 2. Two (Split)
Two media items displayed side-by-side, each taking up 50% of the width.
```
+--------------------------+---------------------------+
|                          |                           |
|        [ Image 1 ]       |        [ Image 2 ]        |
|                          |                           |
+--------------------------+---------------------------+
```

### 3. Three-Two (5-Up)
A two-row layout. Top row contains 3 items; bottom row contains 2 wider items.
```
+----------------+------------------+------------------+
|                |                  |                  |
|   [ Image 1 ]  |    [ Image 2 ]   |    [ Image 3 ]   |
|                |                  |                  |
+----------------+------------------+------------------+
|                                   |                  |
|           [ Image 4 ]             |    [ Image 5 ]   |
|                                   |                  |
+-----------------------------------+------------------+
```

### 4. Three-Three (6-Up)
A two-row layout. Top row contains 3 items; bottom row contains 3 items.
```
+----------------+------------------+------------------+
|                |                  |                  |
|   [ Image 1 ]  |    [ Image 2 ]   |    [ Image 3 ]   |
|                |                  |                  |
+----------------+------------------+------------------+
|                |                  |                  |
|   [ Image 4 ]  |    [ Image 5 ]   |    [ Image 6 ]   |
|                |                  |                  |
+----------------+------------------+------------------+
```

### 5. Four-One (5-Up Variation)
A two-row layout. Top row has 4 items; bottom row has 1 full-width item.
```
+------------+------------+------------+---------------+
|            |            |            |               |
|  [ Img 1 ] |  [ Img 2 ] |  [ Img 3 ] |   [ Img 4 ]   |
|            |            |            |               |
+------------+------------+------------+---------------+
|                                                      |
|                      [ Image 5 ]                     |
|                                                      |
+------------------------------------------------------+
```

### 6. Four-Two (6-Up Variation)
A two-row layout. Top row has 4 items; bottom row has 2 items.
```
+------------+------------+------------+---------------+
|            |            |            |               |
|  [ Img 1 ] |  [ Img 2 ] |  [ Img 3 ] |   [ Img 4 ]   |
|            |            |            |               |
+------------+------------+------------+---------------+
|                          |                           |
|        [ Image 5 ]       |        [ Image 6 ]        |
|                          |                           |
+--------------------------+---------------------------+
```

## Implementation Notes

- **Media Handling**: The component should handle both images and videos seamlessly. Videos should autoplay, loop, and be muted by default.
- **Aspect Ratios**: The grid cells should enforce specific aspect ratios to maintain the layout's integrity, regardless of the source media's dimensions.
- **Admin Configuration**: These options must be exposed as a dropdown or visual selector in the Filament Admin Panel.
