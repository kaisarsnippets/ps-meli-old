/**
* 2015 KaisarCode
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    KaisarCode <kaisar@kaisarcode.com>
*  @copyright 2015 KaisarCode.com
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

function orangeTree(query){
  $(query).html("<ul class='kc-tree'></ul>");
  this.root = $(query).find(".kc-tree");
  this.folderIcon = "<i class=\"icon-folder-close\"></i>";
  this.fileIcon = "<i class=\"icon-file\"></i>";
  this.folderOpenIcon = "<i class=\"icon-folder-open\"></i>";
  this.loadingIcon = "<i class=\"icon-spinner icon-spin\"></i>";
  this.data = [];
  this.id = 0;
}

orangeTree.prototype.addBranch = function(obj){
  //icon variables
  var folder_closed = this.folderIcon;
  var folder_open = this.folderOpenIcon;
  var loading_icon = this.loadingIcon;

  this.id++;

  var id = this.id;
  
  //ATTRIBUTE ID
  var attrid=obj.attrid || '';
  //FOLDER
  var folder = obj.folder || false; //default folder
  //TITLE
  var title = obj.title || id; //default title
  title += "";
  //PATH
  var path = obj.path || "";
  if(path === ""){
    path = id;
  }
  else{
    path = path + "/" + id;
  }
  //ICON
  var icon = this.fileIcon;
  if(folder === true){
    icon = this.folderIcon;
  }
  icon = obj.icon || icon;
  //OPEN
  var open = obj.open || false;
  var display = "block";
  if(open === false){
    display = "none";
  }

  if(open === true && folder === true){
    icon = folder_open;
  }

  var to_push = {
    id: id,
    folder: folder,
    title: title,
    path: path,
    icon: icon,
    open: open
  };
  var base = "<li id=\""+attrid+"\" data-id=\""+id+"\" class=\"kc-tree-file\">" + "<span class=\"kc-tree-branch\"> <span class=\"kc-tree-title\">" + title + "</span></span></li>";
  if(folder === true){
    base = "<li id=\""+attrid+"\" data-id=\""+id+"\" class=\"kc-tree-folder\">" + "<span class=\"kc-tree-branch\"><span class=\"kc-tree-icon\" data-open=\""+open+"\">" + icon + "</span> <span class=\"kc-tree-title\">" + title + "</span></span><ul style=\"display:"+display+"\"></ul></li>";
  } 

  path += "";
  //if it is just in the root
  if(path === ("" + id)){
    //add to root
    this.root.append(base);
  }
  else{
    var final_node = path.split("/").reverse()[1];
    this.root.find("[data-id=\""+final_node+"\"] > ul").append(base);
  }

  //CLICK
  var click = function(){}; //onclick default
  if(obj.click){
    if(folder === true){
      click = function(){
        obj.click();
        
        $(this).parent().find("> ul").slideToggle();

        if($(this).parent().find("> .kc-tree-branch > .kc-tree-icon").attr("data-open") === "true"){
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").html(folder_closed);
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").attr("data-open", "false");
        }
        else{
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").html(folder_open);
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").attr("data-open", "true");
        }
      };
    }
    else{
      click = obj.click;
    }
  }
  else{
    if(folder === true){
      click = function(){
        $(this).parent().find("ul").slideToggle();

        if($(this).parent().find("> .kc-tree-branch > .kc-tree-icon").attr("data-open") === "true"){
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").html(folder_closed);
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").attr("data-open", "false");
        }
        else{
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").html(folder_open);
          $(this).parent().find("> .kc-tree-branch > .kc-tree-icon").attr("data-open", "true");
        }
      };
    }
  }

  to_push.elem = this.root.find("[data-id=\""+id+"\"]");
  to_push.click = click;
  this.data.push(to_push);

  if(folder === true){
    this.root.find("[data-id=\""+id+"\"] > span").click(click);
  }
  else{
    this.root.find("[data-id=\""+id+"\"]").click(click);
  }
  
  return to_push;
  
};

orangeTree.prototype.removeBranch = function(id){
  this.root.find("[data-id=\""+id+"\"]").remove();
}

orangeTree.prototype.openBranch = function(id){
  this.root.find("[data-id=\""+id+"\"] > ul").slideDown();
  this.root.find("[data-id=\""+id+"\"]").find("> span > .kc-tree-branch > .kc-tree-icon").html(this.folderOpenIcon);
}

orangeTree.prototype.closeBranch = function(id){
  this.root.find("[data-id=\""+id+"\"] > ul").slideUp();
  this.root.find("[data-id=\""+id+"\"]").find("> span > .kc-tree-branch > .kc-tree-icon").html(this.folderIcon);
}

orangeTree.prototype.toggleBranch = function(id){
  this.root.find("[data-id=\""+id+"\"] > ul").slideToggle();
  if(this.root.find("[data-id=\""+id+"\"] > .kc-tree-branch > .kc-tree-icon").html() === this.folderOpenIcon){
    this.root.find("[data-id=\""+id+"\"] > .kc-tree-branch > .kc-tree-icon").html(this.folderIcon);
  }
  else{
    this.root.find("[data-id=\""+id+"\"] > .kc-tree-branch > .kc-tree-icon").html(this.folderOpenIcon);
  }
}

orangeTree.prototype.isOpen = function(id){
  return !(this.root.find("[data-id=\""+id+"\"] > ul").css("display") === "none");
}

orangeTree.prototype.getByTitle = function(title){
  var ret = [];
  for(var i = 0; i < this.data.length; i++){
    if(this.data[i].title === title){
      ret.push(this.data[i]);
    }
  }
  return ret;
}

orangeTree.prototype.rename = function(id, title){
  this.root.find("[data-id=\""+id+"\"]").find(".kc-tree-title").html(title);
  for(var i = 0; i < this.data.length; i++){
    if(this.data[i].id === id){
      this.data[i].title = title;
      return this.data[i];
    }
  }
}
