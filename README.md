# Bootstrap Toolbox Carousel (`bt-carousel`)

**Maintenance:** Active  
**Status:** Stable  
**Version:** 1.x  
**Requires:** Drupal 9+, Bootstrap  

## Description

The `bt-carousel` (Bootstrap Toolbox Carousel) module provides a field formatter for `entity_reference` fields, generating a Bootstrap carousel from the referenced entity's content.

### Features

- **Field Formatter:**
  - Allows you to choose the **view mode** of the referenced entity (full, teaser, or any custom display mode).
  - Offers the option to **select specific fields** of the entity to be displayed, with the ability to specify:
    - Carousel image
    - Slide title
    - Description text
    - Custom link

- **Views Plugins:**
  - **Style Plugin**: Renders the content of the entity as a Bootstrap carousel, with options to select the view mode.
  - **Row Plugin**: Similar to the field formatter, it allows selecting fields of the entity that will act as the image, title, and description.
  - Provides the ability to **override fields** for maximum control over the carousel display.

## Installation

1. **Download** the module and place it in the `modules/custom` directory of your Drupal installation.
2. **Enable** the module via the administration interface under `Extend -> Custom` or using Drush:
   ```bash
   drush en bt-carousel -y ```

## Configuration

### Field Formatter

1. Navigate to the entity's administration page that contains the `entity_reference` field you want to format.
2. Under the "Manage Display" tab, select the `Bootstrap Toolbox Carousel` format.
3. Configure the available options:
   - **View Mode**: Choose the entity's view mode.
   - **Select Fields**: Define which fields act as the image, title, and description for each slide.
   - **Custom Link** (optional).

### Views Plugins

1. Create or edit a view that displays entities you want to represent in a carousel.
2. In the view configuration:
   - Select `Bootstrap Toolbox Carousel` as the **Style**.
   - If using the **row plugin**, select the fields and specify which will act as the image, title, and description.
3. Configure options for complete control over the carousel display.

## Customization

The module allows you to override Twig templates to customize the HTML output of the carousel. Copy the templates from the module to your theme and adjust them as needed.

```bash
themes/custom/my_theme/templates/```

### The available template names are:

- `bt-carousel--field-formatter.html.twig`
- `bt-carousel--views-style.html.twig`
- `bt-carousel--views-row.html.twig`

## Requirements

- **Drupal 9+**: Minimum required version.
- **Bootstrap**: Must be included in your theme or site, as the carousel depends on this CSS framework.

## Contributing

Contributions, bug reports, and feature requests are welcome on the project page on [Drupal.org](https://www.drupal.org).

## Maintenance

The module is maintained by [Carlos Espino](https://www.drupal.org/u/carlos-espino). You can contact me via the contact form on my Drupal.org profile page.



