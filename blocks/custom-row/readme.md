# Custom Row
Custom block intended to provided better encapsulation for rows of content. Can be used similar to a "group" block but with background options or be marked for full width so it breaks out of its parent container and will span the full width of the screen.

## Style Notes
When using this block you may need to adjust the parent container to over overflow-x: hidden as the block on full width uses 100vw which does NOT take into account the scrollbar and as such ends up with horizontal scroll between 5-10px.