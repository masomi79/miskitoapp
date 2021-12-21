<template>
    <header>
      <div class="header-wrapp">
        <div class="container">
            <h1><a href="/">miskito.org</a></h1>
        </div>
        <div class="tar">
          <button type="button" class="menu-btn" v-on:click="open=!open">
            <i class="fa fa-bars" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <div class="menu" v-bind:class="{'is-active' : open }">
        <div class="menu__item"><a href="/">TOP</a></div>
        <div class="menu__item"><a href="/home">Home</a></div>
        <div class="menu__item"><a href="https://massumifukuda.work/wp/contact/" tarbet="_blank">CONTACT</a></div>
      </div>
    </header>
</template>
<script>
  export default{
    data(){
      return{
        open: false
      }
    }
  }
</script>
<style scoped>
/*----------------------------
* メニュー開閉ボタン
*----------------------------*/
.menu-btn{
    position: fixed;
    top: 0;
    right: 0;
    z-index: 3;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f8fafc;
    color: #696969;
}

/*----------------------------
* メニュー本体
*----------------------------*/
.menu{
    position: fixed;
    top: 0;
    right: 0;
    z-index: 1;
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: left;
    background: #fff;
    text-align: left;
    padding-top: 53.7px;
}
.menu__item{
    width: 100%;
    height: auto;
    padding: .5em 1em;
    text-align: left;
    color: #696969;
    box-sizing: border-box;
    border-bottom: solid 1px lightgray;
}

/*----------------------------
* アニメーション部分
*----------------------------*/

/* アニメーション前のメニューの状態 */
.menu{
  transform: translateX(100vw);
  transition: all .3s linear;
}
/* アニメーション後のメニューの状態 */
.menu.is-active{
  transform: translateX(0);
}
  .header-wrapp{
    display: table;
    width: 100%;
    position: fixed;
    background-color: #f8fafc;
    border-bottom: solid 1px lightgray;
    z-index: 2;
  }
  .header-wrapp div{
    display: table-cell;
    width: 50%;
  }
  h1{
    margin:0;
    font-size: 1.6rem;
  }
  h1 a{
    color: #EE3B2D;
    text-decoration: none;
    line-height: 3.2rem;
    font-size: 1.2rem;
  }
  h1 a:hover{
    text-decoration: none;
  }
</style>