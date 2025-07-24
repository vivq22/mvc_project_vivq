<form action="index.php?page=checkout_checkout" method="post">
  {{foreach carretilla}}
    <div style="width:100%;display:flex;gap:1rem;padding:0.5rem 1rem;">
      <span>{{productId}}</span>
      <span style="flex:1;">{{productName}}</span>
      <span>{{crrprc}}</span>
      <span>{{crrctd}}</span>
    </div>
  {{endfor carretilla}}
  <button type="submit">Place Order</button>
</form>