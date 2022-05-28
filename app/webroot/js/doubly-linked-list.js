(function (exports) {
  "use strict";
/*
 * Constructor. Takes no arguments.
*/

  function DoublyLinkedList() {
    // pointer to first item
    this._head = null;
    // pointer to the last item
    this._tail = null;
    // length of list
    this._length = 0;
  }

  // Wraps data in a node object.
  DoublyLinkedList.prototype._createNewNode = function (data) {
    var node = {
      data: data,
      next: null,
      prev: null
    };
    return node;
  };

/*
 * Appends a node to the end of the list.
*/
  DoublyLinkedList.prototype.append = function (data) {
    var node = this._createNewNode(data);

    if (this._length === 0) {

      // first node, so all pointers to this
      this._head = node;
      this._tail = node;
    } else {

      // put on the tail
      this._tail.next = node;
      node.prev = this._tail;
      this._tail = node;
    }

	this._updateIndexes(); 
    
	// update count
	this._length++;

    return node;
  };

/*
 * Prepends a node to the end of the list.
*/
  /*DoublyLinkedList.prototype.prepend = function (data) {
    var node = this._createNewNode(data);

    if (this.first === null) {

      // we are empty, so this is the first node
      // use the same logic as append
      this.append(node);
      return;
    } else {

      // place before head
      this._head.prev = node;
      node.next = this._head;
      this._head = node;
    }

    // update count
    this._length++;

    return node;
  };*/

/*
 * Returns the node at the specified index. The index starts at 0.
*/
  DoublyLinkedList.prototype.item = function (index) {
    if (index >= 0 && index < this._length) {
      var node = this._head;
      while (index--) {
        node = node.next;
      }
      return node;
    }
  };
  
/*
 * MARTIN: Returns the index of the item. The item MUST implement the equals() function
*/
  /*DoublyLinkedList.prototype.index = function (id) {
	if(this._head.equals(id)) return 0;
	
	index = -1;
	var node = this._head;
	  while (!node.equals(id)) {
		node = node.next;
		index++;
	  }
	return index + 1;
	
  };*/

/*
 * Returns the node at the head of the list.
*/
  DoublyLinkedList.prototype.head = function () {
    return this._head;
  };

/*
 * Returns the node at the tail of the list.
*/
  DoublyLinkedList.prototype.tail = function () {
    return this._tail;
  };

/*
 * Returns the size of the list.
*/
  DoublyLinkedList.prototype.size = function () {
    return this._length;
  };

/*
 * Removes the item at the index.
*/
  DoublyLinkedList.prototype.remove = function (index) {
	  var node = this.item(index);
	  if(node == null) return;

	  // If it was at the head, advance the head to the next item
	  if(index == 0)
		this._head = this._head.next;
	  // If it was at the tail, advance the tail to the previous item
	  if(index == this._length - 1)
		this._tail = this._tail.prev;

	  // Remove from the list
	  if(node.next)
		node.next.prev = node.prev;
	  if(node.prev)
		node.prev.next = node.next;	  
	  
	  this._updateIndexes();
	  
	  // update count
	  this._length--;
  };
  
  DoublyLinkedList.prototype._updateIndexes = function () {
	  var node = this._head;
	  
	  var index = 0;
	  while(node != null) {
		node.data.setIndex(index)/* = index*/;
		index++;
		node = node.next;
	  }
  };
  
  DoublyLinkedList.prototype.itemById = function (id) {
	  var node = this._head;
	  
	  while(node != null && node.data.id != id) {
		node = node.next;
	  }
	  
	  return node;
  };

  exports.DoublyLinkedList = DoublyLinkedList;
})(typeof exports === 'undefined' ? this['DLL'] = {} : exports);