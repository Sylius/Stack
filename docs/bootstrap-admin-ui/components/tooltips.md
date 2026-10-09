# Tooltips

Add `data-bs-toggle="tooltip"` to an element and set its text with `data-bs-title`:

```twig
<a href="{{ path }}" class="btn btn-icon" data-bs-toggle="tooltip" data-bs-title="{{ 'app.ui.edit'|trans }}">
    {{ ux_icon('tabler:pencil') }}
</a>
```

Bootstrap Admin UI binds a tooltip to every trigger on page load, and again to the triggers of a live component after each of its renders.

## Inside live components

Use `data-bs-title`, not `title`. Bootstrap moves a `title` into `data-bs-original-title`, and Live Component replays that change on the next render. From the second render onwards, the tooltip keeps showing the first title instead of the one sent by the server.
